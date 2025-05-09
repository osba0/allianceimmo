import CodeMirror from 'vue-codemirror';
Vue.use(CodeMirror);

require('summernote/dist/summernote-bs4');
Vue.component(
    'summernote', {
        template: '<textarea>{{value}}</textarea>',
        props: {
            value: {type: String, default: ''},
            disableDragAndDrop: {type: Boolean, default: true},
            height: {type: Number, default: 500},
            toolbarData: {type: Array, default: () => (
                    [
                        ['table', ['table']],
                        ['insert', [ 'hr']],
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['style', 'ul', 'ol', 'paragraph']],
                        ['misc', ['codeview']]
                    ]
                )
            }
        },
        data () {
            return {
                obj: ''
            };
        },
        mounted() {
            let vue = this;
            vue.obj = $(this.$el);
            let option = {
                height: this.height,
                toolbar: this.toolbarData,
                disableDragAndDrop: this.disableDragAndDrop,
                // popover: {
                //     air: [
                //         ['color', ['color']],
                //         ['font', ['bold', 'underline', 'clear']]
                //     ]
                // },
                codemirror: {
                    mode: 'text/html',
                    htmlMode: true,
                    lineNumbers: true,
                    theme: 'monokai'
                }
            };
            option.callbacks = {
                onChange(contents) {
                    vue.$emit('input', contents);
                },
                onInit: function() {
                    // console.log('Summernote is launched');
                }
            };
            vue.obj.summernote(option);

            vue.$on('setValue', (val) => {vue.obj.summernote("code", val);});
        },
    }
);
