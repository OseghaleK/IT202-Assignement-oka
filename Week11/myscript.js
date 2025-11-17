$(document).ready(function () {

    $("#confirmBtn").click(function () {

        let email1 = $("#email1").val().trim();
        let email2 = $("#email2").val().trim();

        $("#errorMsg").hide();

        if (email1 === "" || email2 === "") {
            $("#errorMsg").text("Both fields are required.").show();
            return;
        }

        if (email1 !== email2) {
            $("#errorMsg").text("Emails do NOT match! Please try again.").show();
        } else {
            alert("Success! Email has been confirmed.");
        }
    });

});
