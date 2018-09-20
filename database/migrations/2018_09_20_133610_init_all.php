<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB; // For initialization.

class InitAll extends Migration
{
    /**
     * Run the migrations.
     * 
     * Alternatively, you can upload the init.sql file directly into the database.
     *
     * If, for some reason, this doesn't work, just upload the .sql file.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('bags', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('part_id');
        $table->integer('quantity');
        $table->tinyInteger('marked', 1);
        $table->tinyInteger('delivered', 1);
        $table->integer('delivery_id');
        $table->tinyInteger('delivered_by', 1);
        $table->integer('created_by');
        $table->timestamps();
      });
      
      Schema::create('config', function (Blueprint $table) {
        $table->increments('id');
        $table->string('ckey');
        $table->string('value');
        $table->string('setting_name', 64);
        $table->longText('setting_description');
        $table->timestamps();
      });
      
      DB::raw("
        INSERT INTO `config` (`id`, `ckey`, `value`, `setting_name`, `setting_description`) VALUES
        (1, 'dev_mode', '1', 'Developer Mode', 'Developer mode lets you see everything, including some outputs into console.'),
        (2, 'show_locations', '0', 'Show Locations', 'Enables the \"Locations\" route and provides a link under Data > Locations in the Navbar.'),
        (3, 'auto_generate_url', '0', 'Auto-Generation URL', 'This link is used to auto-generate parts, part profiles, filaments, and printers.'),
        (4, 'generate_from_url_tree', '0', 'Auto-Generate', 'If on, use \"/regenerate\" to regenerate all of the parts, profiles, filaments, and printers from the URL tree. If no URL is set, this won''t work.'),
        (5, 'use_aleph_delivery_method', '1', 'Aleph Delivery Method', 'This adds an input field next to bags during delivery prep to mark which container that bag is in. Those numbers will get entered into a table where they can be viewed again with a button next to the delivery on /deliveries/all.');
      ");
      
      Schema::create('deliveries', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id');
        $table->timestamps();
      });
      
      Schema::create('filaments', function (Blueprint $table) {
        $table->increments('id');
        $table->string('filament_name', 64);
        $table->string('background_color', 7); // Set to 7 because only hexes are allowed.
        $table->string('text_color', 7); // Set to 7 because only hexes are allowed.
        $table->tinyInteger('active', 1);
        $table->timestamps();
      });
      
      DB::raw("
        INSERT INTO `filaments` (`id`, `filament_name`, `background_color`, `text_color`, `active`, `updated_at`, `created_at`) VALUES
        (1, 'Black', '#FFFFFF', '#000000', 1, '2018-09-10 04:00:00', '0000-00-00 00:00:00'),
        (2, 'Lulzbot Green', '#66FF66', '#000000', 1, '2018-09-10 13:22:40', '0000-00-00 00:00:00'),
        (3, 'Hammer Gray', '#4d4d4d', '#FFFFFF', 1, '2018-09-10 04:00:00', '0000-00-00 00:00:00'),
        (4, 'Ghost Gray', '#d9d9d9', '#000000', 0, '2018-09-10 17:57:53', '0000-00-00 00:00:00'),
        (5, 'Ninjaflex Black', '#a71fa7', '#ffffff', 1, '2018-09-18 15:43:39', '2018-09-18 15:43:39'),
        (6, 'Ninjaflex Green', '#00ffa7', '#000000', 1, '2018-09-18 15:47:17', '2018-09-18 15:47:17');
      ");
      
      Schema::create('inventories', function (Blueprint $table) {
        $table->increments('id');
        $table->('part_id');
        $table->integer('location_id');
        $table->integer('to_total');
        $table->integer('from_total');
        $table->timestamps();
      });
      
      Schema::create('locations', function (Blueprint $table) {
        $table->increments('id');
        $table->string('location_name', 255);
        $table->longText('location_description');
        $table->tinyInteger('admin_only', 1);
        $table->tinyInteger('required', 1);
        $table->timestamps();
      });
      
      DB::raw("
        INSERT INTO `locations` (`id`, `location_name`, `location_description`, `admin_only`, `required`, `created_at`, `updated_at`) VALUES
        (1, 'Collections', 'Green/Yellow bins by K and L pods.\r\n', 0, 1, '2018-07-18 03:01:52', '2018-09-18 12:49:49'),
        (2, 'Processing', 'Blue/Black bins by E and F pod. ', 0, 1, '2018-07-18 03:05:50', '2018-09-18 12:49:53'),
        (3, 'Backstock', 'Green Shelves by the door to cluster and above the printer cabinets.', 0, 1, '2018-07-18 03:06:29', '2018-09-18 12:49:56'),
        (4, 'Fails', 'No specific locations. Used to calculate fails, filaments, and rates.', 1, 1, '2018-07-18 03:07:08', '2018-09-18 12:49:59'),
        (5, 'InHouse', 'For parts used in house.', 0, 1, '2018-08-17 20:39:57', '2018-09-18 12:50:02'),
        (6, 'Initial', 'This is used when initializing ACIT.', 1, 1, '2018-09-04 22:58:20', '2018-09-18 12:50:04');
      ");
      
      Schema::create('orders', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('part_id');
        $table->tinyInteger('priority', 1);
        $table->integer('quantity');
        $table->integer('filled');
        $table->tinyInteger('closed', 1);
        $table->string('mo', 32);
        $table->timestamps();
      });
      
      Schema::create('overages', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('part_id');
        $table->integer('delivery_id');
        $table->integer('quantity');
        $table->integer('resolved');
        $table->string('ov_mo', 16);
        $table->string('mo', 32);
        $table->timestamps();
      });
      
      Schema::create('parts', function (Blueprint $table) {
        $table->increments('id');
        $table->string('part_name', 255);
        $table->string('part_serial', 255);
        $table->string('part_version', 255);
        $table->string('part_color', 255)->default("Black");
        $table->float('part_mass');
        $table->float('part_waste');
        $table->integer('recommended_bagging');
        $table->tinyInteger('part_cleaned', 1);
        $table->tinyInteger('in_moratorium', 1);
        $table->timestamps();
      });
      
      Schema::create('password_resets', function (Blueprint $table) {
        $table->string('email')->index();
        $table->string('token')->index();
        $table->timestamp('created_at');
      });
      
      Schema::create('printers', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name', 64);
        $table->tinyInteger('active', 1);
        $table->timestamps();
      });
      
      DB::raw("
        INSERT INTO `printers` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
        (1, 'Mini', 1, '2018-08-24 17:52:27', '2018-08-24 17:52:27'),
        (2, 'Mini2', 1, '2018-08-24 17:52:27', '2018-09-10 13:22:42'),
        (3, 'Taz6', 1, '2018-08-24 17:52:27', '2018-08-22 21:26:38'),
        (4, 'Taz7', 1, '2018-08-24 17:52:27', '2018-09-18 13:29:08');
      ");
      
      Schema::create('print_profiles', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('part_id');
        $table->integer('printer_id');
        $table->integer('lead_time');
        $table->integer('prints');
        $table->tinyInteger('active', 1);
        $table->timestamps();
      });
      
      Schema::create('transfers', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('part_id');
        $table->integer('quantity');
        $table->tinyInteger('reversal');
        $table->integer('to_location_id');
        $table->integer('from_location_id');
        $table->integer('user_id');
        $table->timestamps();
      });
      
      Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->integer('active');
        $table->string('email')->unique();
        $table->integer('account_type');
        $table->string('password', 60);
        $table->rememberToken();
        $table->timestamps();
      });
      
      DB::raw("
        INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `username`, `email`, `password`, `remember_token`, `active`, `admin`, `account_type`, `created_at`, `updated_at`) VALUES
        (5, '', 'Admin', 'Overlord', 'admin', 'admin', '', 1, 1, 2, '2018-07-21 03:03:35', '2018-09-13 18:34:42');
      ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('bags');
      Schema::dropIfExists('config');
      Schema::dropIfExists('deliveries');
      Schema::dropIfExists('filaments');
      Schema::dropIfExists('inventories');
      Schema::dropIfExists('locations');
      Schema::dropIfExists('orders');
      Schema::dropIfExists('overages');
      Schema::dropIfExists('parts');
      Schema::dropIfExists('password_resets');
      Schema::dropIfExists('printers');
      Schema::dropIfExists('printers');
      Schema::dropIfExists('transfers');
      Schema::dropIfExists('users');
    }
}
