$(document).ready(function() {

   $(document).on('change','#QDP_DUITKU_PAYMENT_ENVIRONMENT',function(event){
    $('#QDP_DUITKU_PAYMENT_API_KEY').val('')
    $('#QDP_DUITKU_PAYMENT_MERCHANT_CODE').val('')
    $('#QDP_DUITKU_PAYMENT_EXPIRYPERIOD').val('')
   })
});