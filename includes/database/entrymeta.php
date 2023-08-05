<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Database_EntryMeta extends PFORM_Abstracts_Database
{
    public function __construct()
    {
        parent::__construct(
            'pf_entrymeta'
        );
    }

    /**
     * Function to run our initial
     */
    public function run()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table_name()} (
            `id` int NOT NULL AUTO_INCREMENT,
            `entry_id` int,
            `meta_key` longtext,
            `meta_value` longtext,

            UNIQUE KEY (`id`)
        ) {$this->charset_collate( true )};";

        dbDelta( $query );
    }

}
