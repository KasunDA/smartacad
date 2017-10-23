<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyOrdersItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('item_amount', 10)->index()->default(0)->after('amount');
            $table->smallInteger('discount', false, true)->default(0)->after('item_amount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10)->index()->default(0)->after('amount');
            $table->decimal('amount_paid', 10)->index()->default(0)->after('total_amount');
            $table->smallInteger('discount', false, true)->default(0)->after('amount_paid');
            $table->smallInteger('item_count', false, true)->default(0)->after('tax');
            $table->tinyInteger('is_part_payment', false, true)->default(0)->after('order_initiate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function ($table) {
            $table->dropColumn(['is_part_payment', 'discount', 'amount_paid', 'total_amount']);
        });

        Schema::table('order_items', function ($table) {
            $table->dropColumn(['discount', 'item_amount']);
        });
    }
}
