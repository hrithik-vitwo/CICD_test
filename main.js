function validate()
{
 var email=$("#email").val();
 var pass=$("#pass").val();
 var vendor_code=$("#vendor_code").val();
 var customer_code=$("#customer_code").val();
 
 var email_regex=/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
//  var password_regex1=/([a-z].*[A-Z])|([A-Z].*[a-z])([0-9])+([!,%,&,@,#,$,^,*,?,_,~])/;
  var password_regex2=/([0-9])/;
//  var password_regex3=/([!,%,&,@,#,$,^,*,?,_,~])/;
if(vendor_code){
if(vendor_code=='')
 {
  $("#error_vendor_code").show();
  $("#error_vendor_code").html("Please Enter vendor code");
  $("#error_vendor_code").focus();
  return false;
 }else{
    $("#error_vendor_code").hide();
 }
}
if(customer_code){
if(customer_code=='')
 {
  $("#error_customer_code").show();
  $("#error_customer_code").html("Please Enter Customer code");
  $("#error_customer_code").focus();
  return false;
 }else{
    $("#error_customer_code").hide();
 }
}
if(pass=='')
 {
  $("#error_pass").show();
  $("#error_pass").html("Please Enter Strong Password");
  $("#error_pass").focus();
  return false;
 }else{
    $("#error_pass").hide();
 }
 if(email)
 {
 if(email_regex.test(email)==false)
 {
  $("#email_err").show();
  $("#email_err").html("Please Enter Correct Email");
  $("#email").focus();
  return false;	
 }else{
    $("#email_err").hide();
 }
}

 $(".btn").toggleClass("disabled");
 $(".btn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');


}