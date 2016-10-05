(function() {
  $(document).ready(function() {
    if ($('#redirect').length > 0 && $('#blank_answer').length == 0) {
      $('#question_form').submit();
      return;
    }
  });
})();
