$(document).ready(function () {
  loadProductsMarketCart();

  $("#name").change(function () {
    checkName();
  });
  $("#number_document").change(function () {
    checkNumberDocument();
  });
  $("#phone").change(function () {
    checkPhone();
  });
  $("#email").change(function () {
    checkEmail();
  });
  $("#address").change(function () {
    checkAddress();
  });
  $("#city").change(function () {
    checkCity();
  });

  $("#form__resume__total").on("submit", async (e) => {
    e.preventDefault();
    if(checkName() && checkNumberDocument() && checkPhone() && checkEmail() && checkAddress() && checkCity()){

      let formTemporalBill = new FormData(document.getElementById(e.target.id));
      formTemporalBill.append('products',JSON.stringify(Products_market) );

      let headers = new Headers();
      headers.append('Content-Type', 'application/json');
      headers.append('Accept', 'application/json');

      try {
        
          // Loader
          $("#btn__finish_bill").html("<div class='lds-ring'><div></div><div></div><div></div><div></div></div>");
            const response = await fetch(URL_API + "/temporalbill.php", {
            mode:'cors',
            method: 'POST',
            body: formTemporalBill
          });

          $("#fade").modal({
            fadeDuration: 100
          });
        
          let json = await response.json();
          console.log(json.state);
          $("#modal_pay").modal('show');
          $("#modal_pay .modal-body").html(json.state);
          
          $("#open_modal").trigger("click");
          $("#form__resume__total")[0].reset();
          $(".group-formInput").css("border-color","black");

          // localStorage.setItem("Products_market",[]);
          
          $("#btn__finish_bill").html("FINALIZAR COMPRA");

      } catch (error) {
        console.log(error);
      }

    }else{
        checkName();
        checkNumberDocument();
        checkPhone();
        checkEmail();
        checkAddress();
        checkCity();
    }
  });
});

function checkName() {
  let name = $("#name").val();
  if (name == "") {
    $("#name_err").html("Campo requerido!");
    $("#name_err").css("display", "inline");
    $("#name").css("border-color", "red");
    return false;
  } else if (name.length < 2) {
    $("#name_err").html("Nombre muy corto");
    $("#name_err").css("display", "inline");
    $("#name").css("border-color", "red");
    return false;
  } else {
    $("#name_err").html("");
    $("#name_err").css("display", "none");
    $("#name").css("border-color", "green");
    return true;
  }
}
function checkNumberDocument() {
  if ($("#number_document").val() == "") {
    $("#number_document_err").html("Campo requerido!");
    $("#number_document_err").css("display", "inline");
    $("#number_document").css("border-color", "red");
    return false;
  } else if (!$.isNumeric($("#number_document").val())) {
    $("#number_document_err").html("Solo se permiten números");
    $("#number_document_err").css("display", "inline");
    $("#number_document").css("border-color", "red");
    return false;
  } else {
    $("#number_document_err").html("");
    $("#number_document_err").css("display", "none");
    $("#number_document").css("border-color", "green");
    return true;
  }
}
function checkPhone() {
  if ($("#phone").val() == "") {
    $("#phone_err").html("Campo requerido!");
    $("#phone_err").css("display", "inline");
    $("#phone").css("border-color", "red");
    return false;
  } else if (!$.isNumeric($("#phone").val())) {
    $("#phone_err").html("Solo se permiten números");
    $("#phone_err").css("display", "inline");
    $("#phone").css("border-color", "red");
    return false;
  } else if ($("#phone").val().length < 10 || $("#phone").val().length > 10) {
    $("#phone_err").html("Número invalido");
    $("#phone_err").css("display", "inline");
    $("#phone").css("border-color", "red");
    return false;
  } else {
    $("#phone_err").html("");
    $("#phone_err").css("display", "none");
    $("#phone").css("border-color", "green");
    return true;
  }
}
function checkEmail() {
  let pattern1 = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  let email = $("#email").val();
  let validemail = pattern1.test(email);
  if (email == "") {
    $("#email_err").html("Campo requerido!");
    $("#email_err").css("display", "inline");
    $("#email").css("border-color", "red");
    return false;
  } else if (!validemail) {
    $("#email_err").html("Email invalido");
    $("#email_err").css("display", "inline");
    $("#email").css("border-color", "red");
    return false;
  } else {
    $("#email_err").html("");
    $("#email_err").css("display", "none");
    $("#email").css("border-color", "green");
    return true;
  }
}
function checkAddress() {
  let address = $("#address").val();
  if (address == "") {
    $("#address_err").html("Campo requerido!");
    $("#address_err").css("display", "inline");
    $("#address").css("border-color", "red");
    return false;
  } else {
    $("#address_err").html("");
    $("#address_err").css("display", "none");
    $("#address").css("border-color", "green");
    return true;
  }
}
function checkCity() {
  let city = $("#city").val();
  if (city == "") {
    $("#city_err").html("Campo requerido!");
    $("#city_err").css("display", "inline");
    $("#city").css("border-color", "red");
    return false;
  } else {
    $("#city_err").html("");
    $("#city_err").css("display", "none");
    $("#city").css("border-color", "green");
    return true;
  }
}
