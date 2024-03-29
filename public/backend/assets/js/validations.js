(function ($) {
  "use strict";

  var validation_forms = [];

  $(".validation-form").each(function (ind, form_to_validate) {
    validation_forms[ind] = $(form_to_validate).validate({
      errorElement: "span",
      errorPlacement: function (error, element) {
        error.addClass("invalid-feedback");
        element.closest(".input-group").append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid");
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
      },
      submitHandler: function (form) {
        // Form validation successful, submit the form
        // form.submit();
      },
    });
  });
})(jQuery);
