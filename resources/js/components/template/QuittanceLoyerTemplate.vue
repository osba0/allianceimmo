<template>
    <div>
        <div class="row mb-4">
            <div class="col-md-10 h2" v-html="titleMessage"></div>
            <div class="col-md-2 text-right">
                <button type="button" id="preview-template" class="btn btn-primary" @click="previewModal">Preview 1</button>
            </div>
        </div>
        <div class="row">
            <!-- left block -->
            <div class="col-md-8">
                <div class="mb-4">
                    <label for="name" class="col-md-12 col-form-label text-md-left pb-0 font-weight-bolder pl-0">Nom</label>
                    <div class="d-flex align-items-center py-1 border rounded-lg div-focus border-2">
                        <input v-model="name" type="text" required
                               class="form-control border-0 shadow-none"/>
                    </div>
                </div>

                <!--editor header-->
                <div class="mt-3">
                    <label for="raw-header" class="col-md-12 col-form-label text-md-left pb-0 font-weight-bolder pl-0">Template header</label>
                    <summernote class="form-control"
                                name="editorHeader"
                                v-model="templateHeader"
                                :height="300"
                    ></summernote>
                </div>

                <!--editor body-->
                <div class="mt-3">
                    <label for="raw-body" class="col-md-12 col-form-label text-md-left pb-0 font-weight-bolder pl-0">Template Body</label>
                    <summernote class="form-control"
                                name="editorBody"
                                v-model="templateBody"
                                :height="700"
                    ></summernote>
                </div>

                <!--editor footer-->
                <div class="mt-3">
                    <label for="raw-footer" class="col-md-12 col-form-label text-md-left pb-0 font-weight-bolder pl-0">Template footer</label>
                    <summernote class="form-control"
                                name="editorFooter"
                                v-model="templateFooter"
                                :height="400"
                    ></summernote>
                </div>
            </div>
            <!-- right block -->
            <div class="col-md-4">
                <div v-for="(modelColumns, model) in variables" :key="model" class="mt-4" v-if="model != modelLinesConst">
                    <p class="h4">{{ model }} Variables</p>
                    <div v-for="(description, column) in modelColumns">
                        <span class="font-weight-semi-bold">[[{{ column }}]]</span> - {{ getDescription(description, column) }}
                    </div>
                </div>

                <div class="mt-3">
                    <p class="h4">Code bar size</p>
                    <input v-model="settings.productBarcodeSize" type="range" min="0.1" max="1" step="0.1"
                           :title="settings.productBarcodeSize" class="form-control-range" id="productBarcodeSize"/>
                </div>
                <div class="mt-2">
                    <p class="h4">Product img size</p>
                    <input v-model="settings.productPictureSize" type="range" min="5" max="100" step="1"
                           :title="settings.productPictureSize" class="form-control-range" id="productPictureSize"/>
                </div>
                <div class="mt-2">
                    <p class="h4">Logo size</p>
                    <input v-model="settings.logoSize" type="range" min="5" max="500" step="1"
                           :title="settings.logoSize" class="form-control-range" id="logoSize"/>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <!-- left block -->
            <div class="col-md-8">
                <quittance-loyer-lines-template :is-create="isCreate" :order-lines-data="templateOrderLines"></quittance-loyer-lines-template>
            </div>
            <!-- right block -->
            <div class="col-md-4">
                <div :key="modelLinesConst" class="mt-4">
                    <p class="h4">{{ modelLinesConst }} Variable</p>
                    <div v-for="(description, column) in variables[modelLinesConst]">
                        <span class="font-weight-semi-bold">[[{{ column }}]]</span> - {{ getDescription(description, column) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mt-3">
                <button class="btn btn-success float-right mt-3" @click="confirmBtn(eventClickModelBtn)" v-html="confirmBtnTitle"></button>
            </div>
        </div>

        <div class="modal fade" ref="previewModal" id="previewModal" tabindex="-1" role="dialog"
             aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title h5">Preview</div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center py-1 border border-2 rounded-lg div-focus">
                            <input type="number" min="0" placeholder="test line" required
                                   class="form-control border-0 shadow-none" v-model="orderId">
                        </div>
                        <div class="row mb-2 mt-2">
                            <div class="col">
                                <button type="button" class="btn btn-primary float-right" data-dismiss="modal"
                                        @click="preview"
                                        :disabled="orderId.length === 0"
                                >preview
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

import EventBus from "../../event-bus";
import {ALERT_MSG} from "../../constants";
import QuittanceLoyerLinesTemplate from "./QuittanceLoyerLinesTemplate.vue";


export default {
    name: "QuittanceLoyerTemplate",
    props: {
        isCreate: {type: Boolean, default: false},
        variables: {type: Object, default: false},
        previewUrl: {type: String, default: false},
        listUrl: {type: String, default: false},
        saveUrl: {type: String, default: false},
        templateData: {type: Object, default: false},
        updateUrl: {type: String, default: ''},
        modelLinesConst: {type: String, default: ''},
    },
    components: {
        QuittanceLoyerLinesTemplate,
    },
    data() {
        return {
            titleMessage: 'Quittance Loyer',
            confirmBtnTitle: 'Enregistrer',
            name: this.templateData.name,
            templateHeader: this.templateData.raw.header,
            templateBody: this.templateData.raw.body,
            templateFooter: this.templateData.raw.footer,
            templateOrderLines: this.templateData.raw.orderLines,
            orderId: '',
            eventClickModelBtn: 'onSave',
            settings: this.templateData.raw.settings,
        }
    },
    methods: {
        getDescription(description, column) {
            return description;
            //return translate(`template.${column}`) === `template.${column}` ? description : translate(`template.${column}`)
        },
        previewModal(method = 'show') {
            $(this.$refs.previewModal).modal(method);
        },
        preview() {
            let data = {
                orderId: this.orderId,
                raw: {
                    header: this.templateHeader,
                    body: this.templateBody,
                    footer: this.templateFooter,
                    orderLines: this.templateOrderLines,
                    settings: this.settings,
                },
            }

            axios.post(this.previewUrl, data)
                .then(response => {
                    window.open(response.data.url, '_blank');
                })
                .catch(error => {
                    this.showNotification(error.response.data, 'error');
                });
        },
        onSave() {
            if (this.validate()) {
                this._onSave('post', this.saveUrl, {
                    name: this.name,
                    raw: {
                        header: this.templateHeader,
                        body: this.templateBody,
                        footer: this.templateFooter,
                        orderLines: this.templateOrderLines,
                        settings: this.settings,
                    }
                });
            }
        },
        onUpdate() {
            if (this.validate()) {
                this._onSave('patch', this.updateUrl, {
                    raw: {
                        header: this.templateHeader,
                        body: this.templateBody,
                        footer: this.templateFooter,
                        orderLines: this.templateOrderLines,
                        settings: this.settings,
                    }
                });
            }
        },
        _onSave(method, url, data) {
            alert(method)
            axios[method](url, data)
                .then(response => {

                    setTimeout(this.openList(false), 1000)
                })
                .catch(error => {
                    this.showNotification(error.response.data, 'error');
                });
        },
        openList(withConfirm = true) {
            if (!withConfirm) {
                window.location = this.listUrl;
                return;
            }

            if (confirm('Are you sure? You changes will be lost')) {
                window.location = this.listUrl;
            }
        },
        validate() {
            if (this.name.length === 0) {
                this.showNotification('Please fill name', 'error');
                return false;
            }

            if (this.templateHeader.length === 0) {
                this.showNotification('Please fill header', 'error');
                return false;
            }
            if (this.templateBody.length === 0) {
                this.showNotification('Please fill body', 'error');
                return false;
            }

            if (this.templateOrderLines.length === 0) {
                this.showNotification('Please fill order lines', 'error');
                return false;
            }

            return true;
        },
        setUpdateFields() {
            this.eventClickModelBtn = 'onUpdate';
            this.confirmBtnTitle = 'Enregistrer';
        },
        confirmBtn() {
            if (this.eventClickModelBtn === 'onSave') {
                this.onSave()
            }

            if (this.eventClickModelBtn === 'onUpdate') {
                this.onUpdate();
            }
        },
        showNotification(message, type = 'success') {
          /*  EventBus.$emit(ALERT_MSG, {
                message: message,
                messageType: type,
                messageTime: 3000
            });*/
           Vue.swal.fire('',
                message,
                type
                );
        }
    },
    mounted() {
        if (!this.isCreate) {
            this.setUpdateFields();
        }
    },
}
</script>

<style scoped>
@import '~codemirror/lib/codemirror.css';
@import '~codemirror/theme/monokai.css';
@import '~summernote/dist/summernote-bs4.css';

#preview-template {
    width: 150px;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}
</style>
