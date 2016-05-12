/**
 * Created by Emmanuel on 4/17/2016.
 */

$(function () {
    // Ajax Get Local Governments Based on the state
    getDependentListBox($('#state_id'), $('#lga_id'), '/list-box/lga/');
});