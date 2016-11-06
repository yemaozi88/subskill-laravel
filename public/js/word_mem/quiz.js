(function () {

    Vue.component('quiz-index-header', {
        template: '<div class="page-header">\
        <h1>第{{ index }}問</h1>\
        </div>',
        props: [
            'index'
        ]
    });

    Vue.component('quiz-player', {
        template: '<div>\
        <button class="btn btn-default" :disabled="!enabled" v-on:click="playAudio">再生</button>\
        <audio preload="auto" v-for="(src, index) in audioWavSrcs" v-on:canplay="onCanPlay"\
            v-on:ended="onEnded" ref="audios">\
            <source :src="src" type="audio/wav" >\
            Your browser does not support the audio element.\
        </audio>\
        </div>'
        ,
        data: function () {
            return {
                audioLoadedNum: 0,
                currentPlayingIndex: -1,
                played: false
            }
        },
        props: {
            audioWavSrcs: {
                default: []
            },
            onlyOnce: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            isPlaying: function () {
                return this.currentPlayingIndex != -1;
            },
            enabled: function () {
                // TODO(sonicmisora): this.audioLoadedNum would be added too many times because
                // onCanPlay would be called after onEnded
                return !this.isPlaying && this.audioLoadedNum >= this.audioTotalNum &&
                    (!this.onlyOnce || !this.played);
            },
            audioTotalNum: function () {
                return this.audioWavSrcs.length;
            }
        },
        methods: {
            onCanPlay: function () {
                this.audioLoadedNum++;
            },
            onEnded: function () {
                if (this.currentPlayingIndex == this.audioTotalNum - 1) {
                    // If this is the last one
                    this.currentPlayingIndex = -1;
                    this.played = true;
                    return;
                }
                this.currentPlayingIndex++;
                this.$refs.audios[this.currentPlayingIndex].play();
            },
            playAudio: function () {
                this.currentPlayingIndex = 0;
                if (this.$refs.audios.length == 0) {
                    return;
                }
                this.$refs.audios[0].play();
            }
        }
    });

    Vue.component('wm-answer-sheet', {
        template: ''
    });

    var app = new Vue({
        el: '#app',
        data: {
            step: 0,
            quizIndex: 1,
            audioWavSrcs: [ 'http://localhost/upload/wm/set2/1.wav', 'http://localhost/upload/wm/set2/2.wav']
        },
        computed: {
            showIntro: function () {
                return this.step == 0;
            },
            showQuiz: function () {
                return this.step >= 1;
            }
        },
        methods: {
            introBtnClicked: function () {
                this.step = 1;
            }
        }
    });

}());



