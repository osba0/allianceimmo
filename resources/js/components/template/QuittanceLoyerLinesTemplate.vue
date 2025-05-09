<template>
    <div>
        <div class="mt-2 row">
            <div class="col-6"><span class="h4">Lignes loyers</span></div>
            <div class="col-6 text-right">
                <button type="button" class="btn btn-primary" @click="addOrderLines">Add column</button>
            </div>
        </div>
        <div class="mt-2 form-group row">
            <div class="col-12">
                <label class="control-label">Sort order line list</label>
                <draggable tag="ul" :list="orderLinesList" class="list-group" handle=".handle" @change="afterDrag">
                    <li
                        class="list-group-item"
                        v-for="(element, index) in orderLinesList"
                        :key="index"
                    >
                        <div class="form-inline">
                            <i class="fa fa-align-justify handle col-1"></i>

                            <input type="text" class="form-control col-4 mr-2 orderLineTitle" v-model="element.name" />

                            <div class="mr-2 col-5">
                                <summernote class="form-control"
                                            v-model="element.columnData"
                                            :height="80"
                                            :toolbar-data="toolbarData"
                                            ref="orderLinesColumnData"
                                ></summernote>
                            </div>

                            <button type="button" class="btn btn-danger" @click="removeOrderLines(index)">Delete</button>
                        </div>
                    </li>
                </draggable>
            </div>
        </div>
    </div>
</template>

<script>

import draggable from 'vuedraggable';

export default {
    name: "QuittanceLoyerLinesTemplate",
    props: {
        orderLinesData: {type: Array, required: true},
        isCreate: {type: Boolean, default: false},
    },
    components: {
        draggable,
    },
    data() {
        return {
            orderLinesList: this.orderLinesData,
            toolbarData: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['paragraph']],
            ]
        }
    },
    methods: {
        removeOrderLines(index) {
            this.$delete(this.orderLinesList, index);
        },
        addOrderLines() {
            this.orderLinesList.push({
                "name": "",
                "columnData": ""
            });
        },
        afterDrag() {
            let self = this;
            this.orderLinesList.forEach((item, index) => {
                self.$refs.orderLinesColumnData[index].$emit('setValue', item.columnData);
            })
        }
    },
    mounted() {

    },
}
</script>

<style scoped>

</style>
