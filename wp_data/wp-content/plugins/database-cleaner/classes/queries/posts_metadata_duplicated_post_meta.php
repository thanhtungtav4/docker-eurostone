<?php

class Meow_DBCLNR_Queries_Posts_Metadata_Duplicated_Post_Meta extends Meow_DBCLNR_Queries_Core
{
    public function generate_fake_data_query($age_threshold = 0)
    {
        $id = $this->generate_fake_post( $age_threshold );
        add_post_meta( $id, $this->fake_data_post_metakey, $this->fake_data_metavalue );
        add_post_meta( $id, $this->fake_data_post_metakey, $this->fake_data_metavalue );
    }

    public function count_query($age_threshold = 0)
    {
        global $wpdb;
        $result = $wpdb->get_var(
            "
			SELECT COUNT(t1.meta_id) FROM $wpdb->postmeta t1 
			INNER JOIN $wpdb->postmeta t2  
			WHERE  t1.meta_id < t2.meta_id 
			AND  t1.meta_key = t2.meta_key 
			AND t1.post_id = t2.post_id
			"
        );
        return $result;
    }

    public function delete_query($deep_deletions_enabled, $limit, $age_threshold = 0)
    {
        global $wpdb;
        $count = $this->count_query();
        if ($count === 0) {
            return 0;
        }

        $potential_duplicated_postmeta = $wpdb->get_results( "
            SELECT
                t1.*
            FROM
                $wpdb->postmeta t1
            INNER JOIN (
                SELECT DISTINCT
                    t1.post_id,
                    t1.meta_key
                FROM
                    $wpdb->postmeta t1
                    INNER JOIN $wpdb->postmeta t2
                WHERE
                    t1.meta_id < t2.meta_id
                    AND t1.meta_key = t2.meta_key
                    AND t1.post_id = t2.post_id
            ) d ON t1.post_id = d.post_id AND t1.meta_key = d.meta_key;
        ", OBJECT );

        $grouped = array_reduce( $potential_duplicated_postmeta, function ( $result, $item ) {
            $key = $item->post_id . '-' . $item->meta_key;
            if ( !isset( $result[$key] ) ) {
                $result[$key] = [];
            }
            $result[$key][] = $item;
            return $result;
        }, [] );

        $meta_ids = [];
        foreach ( $grouped as $group ) {
            $meta_values = array_map( function ( $item ) {
                return $item->meta_value;
            }, $group );
            $value_counts = array_count_values($meta_values);

            $duplicates = array_filter( $group, function ( $item ) use ( $value_counts ) {
                return $value_counts[ $item->meta_value ] > 1;
            });

            // Left the latest record and retrieved the else meta_ids.
            $target_duplicates = array_slice( $duplicates, 0, -1 );
            $meta_ids = array_merge(
                $meta_ids,
                array_map( function ( $item ) {
                    return $item->meta_id;
                }, $target_duplicates )
            );
        }

        if ( count( $meta_ids ) === 0 ) {
            return 0;
        }

        if ( $deep_deletions_enabled ) {
            return MeowPro_DBCLNR_Queries::delete_posts_metadata_duplicated_post_meta( $meta_ids );
        }

        $placeholder = implode( ', ', array_fill( 0, count( $meta_ids ), '%d' ) );
        $result = $wpdb->query( $wpdb->prepare(
            "
			DELETE FROM $wpdb->postmeta
			WHERE meta_id IN ($placeholder)
            LIMIT %d
			",
			array_merge( $meta_ids, [ $limit ] )
        ) );
        if ($result === false) {
            throw new Error('Failed to delete the duplicated post meta. : ' . $wpdb->last_error);
        }
        return $result;
    }

    public function get_query($offset, $limit, $age_threshold = 0)
    {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare(
            "
			SELECT t1.*
			FROM $wpdb->postmeta t1
			INNER JOIN $wpdb->postmeta t2
			WHERE  t1.meta_id < t2.meta_id
			AND  t1.meta_key = t2.meta_key
			AND t1.post_id = t2.post_id
			LIMIT %d, %d
			",
			$offset, $limit
        ), ARRAY_A );

        return $result;
    }
}
