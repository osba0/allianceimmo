<template>
    <div>
        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <label class="text-nowrap mr-2 mb-0">Nbre de ligne par Page</label>
                <select class="form-control form-control-sm" v-model="paginate" @change="fetchPaiements">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>
            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#addNew" v-on:click="newModal">
                <i class="fa fa-plus-square"></i> Ajouter Paiement
            </button>
        </div>

        <!-- Filtres -->
        <div class="row mb-3">
            <div class="col">
                <label>👤 Locataire</label>
                <select class="form-control" v-model="filters.locataire_id" @change="fetchPaiements">
                    <option value="">Tous</option>
                    <option v-for="locataire in locataires?.data" :key="locataire.id" :value="locataire.id">
                        {{ locataire.nom }} {{ locataire.prenom }}
                    </option>
                </select>
            </div>
            <div class="col">
                <label>🏠 Bail</label>
                <select class="form-control" v-model="filters.bail_id" @change="fetchPaiements">
                    <option value="">Tous</option>
                    <option v-for="bail in baux?.data" :key="bail.id" :value="bail.id">
                        {{ bail.identifiant }} - {{ bail.locataire_nom }} {{ bail.locataire_prenom }}
                    </option>
                </select>
            </div>
            <div class="col">
                <label>📊 Statut</label>
                <select class="form-control" v-model="filters.statut" @change="fetchPaiements">
                    <option value="">Tous</option>
                    <option value="partiel">Partiel</option>
                    <option value="complet">Complet</option>
                    <option value="avance">Avance</option>
                    <option value="arriéré">Arriéré</option>
                </select>
            </div>
            <div class="col">
                <label>📅 Date début</label>
                <input type="date" class="form-control" v-model="filters.date_debut" @change="fetchPaiements">
            </div>
            <div class="col">
                <label>📅 Date fin</label>
                <input type="date" class="form-control" v-model="filters.date_fin" @change="fetchPaiements">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="bg-white">
                    <tr>
                        <th>👤 Locataire</th>
                        <th>🏠 Bail</th>
                        <th>💰 Montant</th>
                        <th>📊 Statut</th>
                        <th>📅 Date Paiement</th>
                        <th>👨‍💼 Utilisateur</th>
                        <th class="text-right">⚙️ Action</th>
                    </tr>
                    <tr>
                        <th colspan="9" class="position-relative p-0">
                            <div class="loader-line" :class="[isLoading ? 'd-block' : 'd-none']"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="paiement in paiements.data" :key="paiement.id">
                        <td class="align-middle">{{ paiement.bail.locataire.nom }} {{ paiement.bail.locataire.prenom }}</td>
                        <td class="align-middle">{{ paiement.bail.reference }}</td>
                        <td class="align-middle">{{ paiement.montant }} FCFA</td>
                        <td class="align-middle">
                            <span class="badge" :class="{
                                'badge-success': paiement.statut === 'complet',
                                'badge-warning': paiement.statut === 'partiel',
                                'badge-primary': paiement.statut === 'avance',
                                'badge-danger': paiement.statut === 'arriéré'
                            }">
                                {{ paiement.statut }}
                            </span>
                        </td>
                        <td class="align-middle">{{ paiement.date_paiement }}</td>
                        <td class="align-middle">{{ paiement.user ? paiement.user.name : 'Système' }}</td>
                        <td class="text-right align-middle">
                            <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="paiements"
                :limit="10"
                @pagination-change-page="fetchPaiements"
            ></pagination>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><strong>Ajouter un paiement</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="createPaiement">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Bail</label>
                                <select class="form-control" v-model="form.bail_id">
                                    <option v-for="bail in baux.data" :value="bail.id">{{ bail.identifiant }} - {{ bail.locataire_nom }} {{ bail.locataire_prenom }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Montant</label>
                                <input v-model="form.montant" type="number" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Date de paiement</label>
                                <input v-model="form.date_paiement" type="date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Mode de paiement</label>
                                <select class="form-control" v-model="form.mode_paiement">
                                    <option value="espèces">Espèces</option>
                                    <option value="virement">Virement</option>
                                    <option value="chèque">Chèque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Période</label>
                                <input v-model="form.periode_paiement" type="month" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Statut</label>
                                <select class="form-control" v-model="form.statut">
                                    <option value="partiel">Partiel</option>
                                    <option value="complet">Complet</option>
                                    <option value="avance">Avance</option>
                                    <option value="arriéré">Arriéré</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            paiements: {
                data: []
            },
            locataires: [],
            baux: [],
            paginate: 10,
            filters: {
                locataire_id: '',
                bail_id: '',
                statut: '',
                date_debut: '',
                date_fin: '',
            },
            isLoading: false,
            form: {
                bail_id: '',
                montant: '',
                date_paiement: '',
                mode_paiement: '',
                periode_paiement: '',
                statut: ''
            }
        };
    },
    methods: {
        async fetchPaiements(page = 1) {
            this.isLoading = true;
            try {
                let response = await axios.get(`/operations/paiement-loyers/listing`, {
                    params: {
                        page: page,
                        paginate: this.paginate,
                        ...this.filters
                    }
                });
                this.paiements = response.data;
            } catch (error) {
                console.error("Erreur lors du chargement des paiements", error);
            }
            this.isLoading = false;
        },

        async fetchLocataires() {
            try {
                let response = await axios.get(`/locat/listing`);
                this.locataires = response?.data;
                console.log('locataires', this.locataires)
            } catch (error) {
                console.error("Erreur lors du chargement des locataires", error);
            }
        },

        async fetchBaux() {
            try {
                let response = await axios.get(`/bail/listing`);
                this.baux = response?.data;
            } catch (error) {
                console.error("Erreur lors du chargement des baux", error);
            }
        },
        newModal() {
            this.form = { bail_id: '', montant: '', date_paiement: '', mode_paiement: '', periode_paiement: '', statut: '' };
        }
    },
    mounted() {
        this.fetchPaiements();
        this.fetchLocataires();
        this.fetchBaux();
    }
};
</script>

<style scoped>
.loader-line {
    height: 4px;
    background: #007bff;
    animation: loading 1s infinite;
}
@keyframes loading {
    0% { width: 0%; }
    50% { width: 50%; }
    100% { width: 100%; }
}
</style>
