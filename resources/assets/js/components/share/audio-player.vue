<template>

<span>
    <audio v-for="(url, index) in urls" preload="auto" v-on:canplay="onCanPlay(index)"
            v-on:ended="onEnded(index)" ref="audios">
        <source :src="url" type="audio/wav">
        Your browser does not support the audio element.
    </audio>
</span>

</template>

<script>
export default {
    data () {
        return {
            loadedNum: 0,
            playedNum: 0
        }
    },
    props: {
        urls: Array,
        /* Time to wait before next audio. */
        interval: {
            type: Number,
            default: 1
        }
    },
    computed: {
        // TODO(sonicmisora): this.audioLoadedNum would be added too many times because
        // onCanPlay would be called after onEnded
        allLoaded () {
            return this.loadedNum >= this.urls.length;
        }
    },
    methods: {
        play () {
            if (this.$refs.audios.length == 0) {
                return;
            }
            this.playedNum = 0;
            this.$refs.audios[0].play();
        },
        onCanPlay (index) {
            this.loadedNum++;
            if (this.loadedNum == this.urls.length) {
                this.$emit("loaded");
            }
        },
        onEnded (index) {
            this.playedNum++;
            if (this.playedNum == this.urls.length) {
                this.$emit("ended"); 
            } else {
                setTimeout(() => {
                    this.$refs.audios[index + 1].play();
                }, this.interval * 1000);
            }
        },
        reload() {
            this.playedNum = 0;
            this.loadedNum = 0;
            for (var i = 0; i < this.urls.length; i++) {
                this.$refs.audios[i].load();
            }
        }
    }
}
</script>