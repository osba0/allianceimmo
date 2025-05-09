<template>
    <div>
        <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>#</th>
                    <th>Template</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
            <tbody>
                <template v-if="!templates.data || !templates.data.length">
                    <tr><td colspan="4" class="bg-white text-center" v-if="checking">Aucun résultat!</td></tr>
                </template>
                <tr v-for="tpl in templates.data" :key="tpl.id">
                    <td class="align-middle"><h5 class="mb-0"><label class="badge badge-primary mb-0">{{tpl.id}}</label></h5></td>
                    <td><label class="mb-0 text-primary font-weight-bold d-block">{{tpl.name}}</label></td>

                    <td class="text-right">
                         <button @click="updateTemplate(tpl)" class="btn btn-primary btn-sm" title="Editer">
                            <span aria-hidden="true" class="fa fa-pencil pt-1 pr-1"></span>
                        </button>


                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</template>
<script type="text/ecmascript-6">

import EventBus from "../../event-bus";
import {ALERT_MSG} from "../../constants";

export default {
    props: {
        url: {type: String, required: true},
        type: {type: Number, required: true},
        defaultIds: {type: Array, required: false, default: () => ([])},
    },
    data() {
        return {
            isLoading: true,
            checking: false,
            templates : {},
            paginate: 1
        }
    },
    methods: {
        copyTemplate(item) {
            window.location.href = `/templates/create?id=${item.id}&type=${item.type}`;
        },
        deleteTemplate(item) {
            if (confirm(translate('template.are_you_sure_delete'))) {
                axios.delete(`/templates/${item.id}`)
                    .then(response => {
                        this.$refs.templateTable.getData();
                    })
                    .catch(error => {
                        EventBus.$emit(ALERT_MSG, {
                            message: error.response.data.message || error.response.data || error || translate('common.error'),
                            messageType: 'error',
                        });
                    });
            }
        },
        updateTemplate(item) {
            window.location = `/templates/${item.id}/edit?type=${this.type}`;
        },
        getTemplate(page=1){

            this.isLoading = true;
            axios.get(this.url+"?paginate="+ this.paginate+'&page=' + page).then(responses => {
              console.log(responses);
              this.templates = responses.data;
              this.isLoading = false;
              this.checking = true;
            }).catch(errors => {

            // react on errors.

            })
        },
    },
     mounted() {
        this.getTemplate(1);

    }
}

</script>

