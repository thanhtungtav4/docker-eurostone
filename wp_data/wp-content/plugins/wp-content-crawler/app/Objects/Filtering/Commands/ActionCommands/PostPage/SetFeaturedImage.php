<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/04/2022
 * Time: 15:36
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage;

use Illuminate\Support\Str;
use WP_Post;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\MultipleMediaItemWithLabel;

class SetFeaturedImage extends AbstractActionCommand {

    /** @var WP_Post|null|false Caches the featured image. See {@link getImage()}. */
    private $image = false;

    public function getKey(): string {
        return CommandKey::SET_FEATURED_IMAGE;
    }

    public function getName(): string {
        return _wpcc('Set featured image');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_POST_PAGE];
    }

    protected function isOutputTypeSameAsInputType(): bool {
        return true;
    }

    public function doesNeedSubjectValue(): bool {
        return false;
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function createViews(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(MultipleMediaItemWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Featured image IDs'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Define the ID of an image that will be used as'
                    . ' the featured image of the post. If you define multiple image IDs, one of them will be used'
                    . ' randomly.')
                )
                ->setVariable(ViewVariableName::NAME, InputName::FEATURED_IMAGE_IDS));
    }

    protected function onExecute($key, $subjectValue) {
        // The data source must be a PostData
        $dataSource = $this->getDataSource();
        if (!($dataSource instanceof PostData)) {
            return;
        }

        // Get the featured image. If the image does not exist, stop.
        $image = $this->getImage();
        if (!$image) {
            return;
        }

        // The image exists. Assign the featured image ID to post data so that it will be used when saving the post.
        $dataSource->setFeaturedImageId($image->ID);

        // The rest of the method is about informing the user. If there is no logger, no need to inform the user.
        $logger = $this->getLogger();
        if (!$logger) {
            return;
        }

        $imageUrl = wp_get_attachment_url($image->ID);
        if (!$imageUrl) {
            return;
        }

        $logger->addMessage(sprintf(
            _wpcc('The featured image is set as the image with ID %1$s. Image URL: %2$s'),
            $image->ID,
            $imageUrl
        ));
    }

    /**
     * @return WP_Post|null If found, one of the images whose ID is given as an input. Otherwise, null. If this method
     *                      was called previously, the same value will be returned. So, the result is consistent.
     * @since 1.12.0
     */
    protected function getImage(): ?WP_Post {
        if ($this->image === false) {
            $this->image = $this->findImage();
        }

        return $this->image;
    }

    /**
     * Retrieves the image IDs defined as an input, validates them, finds a random image ID, and returns its
     * {@link WP_Post}.
     *
     * @return WP_Post|null If found, an image that has one of the image IDs given as an input. Otherwise, null.
     * @since 1.12.0
     */
    protected function findImage(): ?WP_Post {
        $ids = $this->getIds();
        if (!$ids) {
            return null;
        }

        // Select a random image ID
        $randomKey = array_rand($ids, 1);
        $selectedId = is_int($randomKey)
            ? ($ids[$randomKey] ?? null)
            : null;
        if ($selectedId === null) {
            return null;
        }

        $images = $this->getLogger()
            ? $this->findImages($ids)
            : $this->findImages([$selectedId]);

        return $images[$selectedId] ?? null;
    }

    /**
     * Finds the images having the given IDs and reports the invalid IDs to the user
     *
     * @param int[] $rawIds IDs of the images that will be validated
     * @return array<int, WP_Post> {@link WP_Post}s having the given IDs. The keys are the IDs of the posts. If none of
     *                             the given IDs is valid, an empty array is returned.
     * @since 1.12.0
     */
    protected function findImages(array $rawIds): array {
        // WordPress stores the images as posts as well. Get the posts having the given IDs.
        /** @var WP_Post[] $result */
        $result = get_posts([
            'numberposts' => -1,
            'orderby'     => 'post__in',
            'include'     => $rawIds,
            'post_status' => 'any',
            'post_type'   => 'attachment',
        ]);

        // Create an array that maps the post IDs to the posts.
        /** @var array<int, WP_Post> $images */
        $images = [];
        foreach($result as $image) {
            if (!($image instanceof WP_Post)) continue;

            $images[$image->ID] = $image;
        }

        // Report the invalid IDs so that the user can fix the issue
        $invalidIds = array_diff($rawIds, array_keys($images));
        $this->reportInvalidIds($invalidIds);

        return $images;
    }

    /**
     * Adds an information and log message that reports the given invalid IDs
     *
     * @param array $invalidIds IDs of images that are invalid
     * @since 1.12.0
     */
    protected function reportInvalidIds(array $invalidIds): void {
        if (!$invalidIds) return;

        // Prepare the invalid IDs as strings
        $invalidIdsStrArray = [];
        foreach($invalidIds as $invalidId) {
            if ($invalidId === null) continue;

            $invalidIdsStrArray[] = Str::limit((string) $invalidId);
        }

        // If the array is empty, stop.
        if (!$invalidIdsStrArray) return;

        $message = sprintf(
            _wpcc('These image IDs are not valid: %1$s'),
            implode(', ', $invalidIdsStrArray)
        );

        $logger = $this->getLogger();

        Informer::addInfo($message)->addAsLog();
        if ($logger) $logger->addMessage($message);
    }

    /*
     *
     */

    /**
     * @return int[]|null The IDs assigned to the input of the command
     * @since 1.12.0
     */
    public function getIds(): ?array {
        $rawIds = $this->getOption(InputName::FEATURED_IMAGE_IDS);
        if (!is_array($rawIds) || !$rawIds) {
            return null;
        }

        // Parse the given raw IDs into integers
        /** @var int[] $ids */
        $ids = array_filter(array_map(function($data) {
            $id = $data[SettingInnerKey::ITEM_ID] ?? null;
            return is_numeric($id)
                ? (int) $id
                : null;
        }, $rawIds));

        return $ids ?: null;
    }

}