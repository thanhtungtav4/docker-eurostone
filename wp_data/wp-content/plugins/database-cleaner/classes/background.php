<?php



class Meow_DBCLNR_Background
{
	public $core = null;

  public function __construct( $core ) {
		$this->core = $core;
    if ( !wp_next_scheduled( 'dbclnr_cron_tasks' ) ) {
      wp_schedule_event( time(), 'hourly', 'dbclnr_cron_tasks' );
    }
    if ( !wp_next_scheduled( 'dbclnr_cron_analytics' ) ) {
      wp_schedule_event( time(), 'twicedaily', 'dbclnr_cron_analytics' );
    }
    add_action( 'dbclnr_cron_tasks', array( $this, 'tasks' ) );
    add_action( 'dbclnr_cron_analytics', array( $this, 'analytics' ) );
	}

  public function analytics() {
    //$this->core->log( "[Cron] Analytics started." );
    $this->core->refresh_database_size();
    //$this->core->log( "[Cron] Analytics finished." );
  }

  public function tasks() {
    //$this->core->log( "[Cron] Tasks started." );
    //$this->core->refresh_database_size();
    //$this->core->log( "[Cron] Tasks finished." );
  }
}

// TODO: WE should do this when the plugin is desactivated
// wp_clear_scheduled_hook( 'dbclnr_cron' );