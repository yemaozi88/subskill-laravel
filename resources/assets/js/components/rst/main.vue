<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h1>回答方法の説明</h1>
                </div>
                <p>
                    こんにちは{{ username }}さん。{{ groupName }}
                </p>
                <p>
                    これはあなたが一度にどれだけの英文を処理できるかを測定するテストです。<br>
                    テストが始まると、数文の英文が一定の時間に表示されます。
                </p>
                <p>
                    時間が切れると、解答欄が表示されます。<br>
                    それぞれの文章の最後の単語、および内容の正誤を答えてください。
                </p>
                <p>
                    問題は全部で<strong>{{ questionNum }}</strong>問です。
                </p>
                <button class="btn btn-primary">
                    始める
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                name: "123"
            };
        },
        props: {
            username: {
                default: "Default_Username"
            },
            groupName: {
                default: "Default_Group_Name"
            },
            questionNum: {
            }
        }
    }
</script>

<style>
</style>