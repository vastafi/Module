// $("#checkout_paymentDetails").change(function() {
//     if ($(this).val() === "Credit Card") {
//         $('#ccd').show();
//          $('#ccd').attr('required', '');
//          $('#ccd').attr('data-error', 'This field is required.');
//     } else {
//         $('#ccd').hide();
//         $('#ccd').removeAttr('required');
//         $('#ccd').removeAttr('data-error');
//     }
// });
//
// $("#checkout_paymentDetails").trigger("change");
$(document).ready(function () {
    hideCCD();
});
$(document).on('click', '#checkout_paymentDetails', function () {
    hideCCD();
});

function hideCCD(){
    if($("#checkout_paymentDetails").val() == "Credit Card"){
        $('#ccd').show();
    }
    else {
        $('#ccd').hide();
    }
}