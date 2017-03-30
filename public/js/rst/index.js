(function() {
    $(document).ready(function() {
        $("form").submit(function() {
            // TODO: perform this validation in backend
            var username = $("#username").val();
            var validationResult = Helpers.validateUsername(username);
            if (!validationResult[0]) {
                alert(validationResult[1]);
                return false;
            }
        });
    });
})();
