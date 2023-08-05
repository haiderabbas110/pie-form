<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Database_Fields extends PFORM_Abstracts_Database
{
    public function __construct()
    {
        parent::__construct(
            'pf_fields'
        );
    }

    /**
     * Function to run our initial
     */
    public function run()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table_name()} (
            `id` int NOT NULL AUTO_INCREMENT,
            `form_id` int,
            `form_title` longtext,
            `form_data` longtext,
            `form_type` varchar(15),
            `created_at` TIMESTAMP,
            `updated_at` DATETIME,

            UNIQUE KEY (`id`)
        ) {$this->charset_collate( true )};";

        dbDelta( $query );
    }

}
