(function() {
  $(document).ready(function() {
    $("form").submit(function() {
      // TODO: perform this validation in backend
      if ($("#username").val().length < 4) {
        alert("Username must be longer than 3 characters.");
        return false;
      }
    });
  });
})();
