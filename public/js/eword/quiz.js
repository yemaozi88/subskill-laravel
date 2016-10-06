(function() {
    $(document).ready(function() {

        var playBtn = $('#play_audio');
        var audio = $('#question_audio');
        var with_wav = true;
        if (playBtn.length == 0) {
            with_wav = false;
        }
        var qForm = $('#question_form');
        var qRadioBtns = $('#question_form input[type="radio"]');
        var timerText = $('.timer span');
        // How many times play button has been pressed
        var playCount = 0;
        var firstPlayed = false;
        // TODO: make time limit configurable
        var timeLimit = 20.0;
        // TODO: use real delta time instead of this varibale
        // Update timer frequency
        var deltaTime = 0.01;
        var elapsedTime = 0.0;
        var intervalHandler = null;
        // Time up used for submitting
        var isTimeUp = false;

        function refreshRemainingTime() {
            var rTime = timeLimit - elapsedTime;
            timerText.text(Math.round(rTime * 100)/100);
        }

        function startTimer() {
            intervalHandler = setInterval(function() {
                elapsedTime += deltaTime;
                if (elapsedTime >= timeLimit) {
                    elapsedTime = timeLimit;
                    stopTimer();
                    isTimeUp = true;
                    qForm.submit();
                }
                refreshRemainingTime();
            }, Math.round(deltaTime * 1000));
        }

        function stopTimer() {
            clearInterval(intervalHandler);
            intervalHandler = null;
        }

        function setPlayButtonDisabled(value) {
            playBtn[0].disabled = value;
        }

        if (with_wav) {
            playBtn.click(function() {
                if (!firstPlayed) {
                    qRadioBtns.each(function() {
                        $(this)[0].disabled = false;
                    });
                    startTimer();
                    firstPlayed = true;
                }
                setPlayButtonDisabled(true);
                audio[0].play();
                playCount++;
            });

            audio.bind("canplay", function() {
                if (!firstPlayed) {
                    setPlayButtonDisabled(false);
                }
            });
            audio.bind("ended", function () {
                setPlayButtonDisabled(false);
            });
        } else {
            qRadioBtns.each(function() {
                $(this)[0].disabled = false;
            });
            startTimer();
        }

        // Update some fields used by js before submitting
        qForm.submit(function (event) {
            var playCountField = $('#play_count');
            playCountField.val(playCount);
            var elapsedTimeField = $('#elapsed_time');
            elapsedTimeField.val(elapsedTime);
            if (isTimeUp) {
                qRadioBtns.each(function() {
                    $(this)[0].checked = false;
                });
                $('#q_selection_blank')[0].checked = true;
            }
            stopTimer();
        });

        qRadioBtns.bind('change', function(event) {
            if (event.target.checked) {
                qForm.submit();
            }
        });

        // Show time limit
        refreshRemainingTime();
    });
})();
