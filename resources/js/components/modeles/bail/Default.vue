<template>
	<div>
		<div class="modal fade fullscreenModal" id="previsualitionBail" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="preVisual" aria-hidden="true">
                
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5  v-if="!generateFile" class="modal-title text-uppercase"><strong><span class="text-primary"><u>Prévisualisation</u></span> Contrat de Bail</strong></h5>
                        <h5  v-if="generateFile" class="modal-title text-uppercase"><strong><span class="text-primary"><u>Contrat de Bail</u></span> Final</strong></h5>
                        <button type="button" class="close" ref="closePopupPrevisual" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-body modalinfo">
                          <embed :src="bail_previsualise" frameborder="0" width="100%" height="450px">    
                    </div>
                     <div class="modal-footer justify-content-center">
                        <button  v-if="!generateFile" type="button" class="btn btn-success btn-lg" @click="validation_bail()">Valider</button>
                        <button  type="button" class="btn btn-secondary btn btn-lg" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
	</div>
</template>
<script>
	// Pdf
	import { PdfMakeWrapper, Table, QR, Img} from 'pdfmake-wrapper';

	import { ITable } from 'pdfmake-wrapper/lib/interfaces'; 

	import pdfFonts from "pdfmake/build/vfs_fonts";

	import { EventBus } from '../../../event-bus';

	export default {
	    props: [],
		components: {
		},
	    data() { 
	        return {
	           bail_previsualise: null,
	           name_file: '',
	           form: {},
	           generateFile: false
	        }
	    },
	    methods: {
	        validation_bail(){
	        	EventBus.$emit('SAVE_BAIL', { 
	               file_genered: this.bail_previsualise,
	               name_file: this.name_file
	            });
	        },
	        convertImgToBase64(url, callback, outputFormat){
	            var canvas = document.createElement('CANVAS');
	            var ctx = canvas.getContext('2d');
	            var img = new Image;
	            img.crossOrigin = 'Anonymous';
	            img.onload = function(){
	                canvas.height = img.height;
	                canvas.width = img.width;
	                ctx.drawImage(img,0,0);
	                var dataURL = canvas.toDataURL(outputFormat || 'image/png');
	                callback.call(this, dataURL);
	                // Clean up
	                canvas = null; 
	            };
	            img.src = url;
	        },
	       	generatePdf(generate_real_bail=false){

		        PdfMakeWrapper.setFonts(pdfFonts);

		        const pdf = new PdfMakeWrapper();

		        //pdf.pageOrientation('landscape'); //Option Paysage

		        pdf.pageMargins([ 40, 100, 40, 70 ]);
		        pdf.defaultStyle({
		            fontSize: 10
		        });
		        
		        var that  = this;

		        this.convertImgToBase64('/assets/images/logo_ab_immo.PNG', function(base64Img){

		        	 // formater le nom du fichier

		            var nameFile = "bail-"+that.form.agence.agence_nom.replace(/[^a-z0-9]/gi, '').toLowerCase()+"-"+that.form.proprio.proprio_prenom.replace(/[^a-z0-9]/gi, '').toLowerCase()+"-"+that.form.proprio.proprio_nom.replace(/[^a-z0-9]/gi, '').toLowerCase()+"-"+that.randomIntFromInterval(0, 9999)+".pdf";

		            var logo_entete=[], entete=[];
		            logo_entete.push({stack: [{ image: base64Img, width: 60}]}); 

		            var text_entete = [[{text: that.form.agence.agence_nom.toUpperCase(), fontSize: 25, color: '#446da9', noWrap: true , bold: true, margin:[0, 20, 0, 0], alignment: 'left'}], [{text: that.form.agence.agence_activite, fontSize: 11, noWrap: false , bold: true, alignment: 'left'}]];  

		            entete.push([logo_entete, text_entete, new QR(location.origin+"/assets/bail/"+nameFile).fit(60).alignment('right').end]); 

		            /*Footer settings*/

		            var text_footer = [{text: that.form.agence.agence_nom.toUpperCase()+' '+that.form.agence.agence_ville +' ('+that.form.agence.agence_pays+') '+that.form.agence.agence_adresse+' Téléphone:'+that.form.agence.agence_ind1+' '+that.form.agence.agence_tel1+' NINEA '+that.form.agence.agence_ninea+' '+that.form.agence.agence_email, fontSize: 11, color: '#7e7e7e', noWrap: false , bold: false, alignment: 'center', width: '*'}];


		            /* Content */

		            var title = new Table([[{text: 'Contrat de Location', fontSize: 23, noWrap: false , bold: true, alignment: 'center'}]]).widths('100%').margin([0, 5, 0, 0]).end;

		            var title_and_qrcode = new Table([[title, new QR(location.origin+"/assets/bail/"+nameFile).fit(75).alignment('right').end]]).widths(['*', 80]).layout('noBorders').margin([0, 0, 0, 0]).end;

		           

		            // Le bail
		            var bail = [];

		            bail.push([{text: "I. DESIGNATION DES PARTIES\n", fontSize:12, bold: true }]);

		            bail.push([{text: "\nLe présent contrat de bail est conclu entre", fontSize:12, bold: false}]);

		            bail.push([{text: "\n\u200B\t\t\tD’une part :\n", fontSize:12, bold: true}]);

		            bail.push([{text: [{text: "\nMonsieur ", italics: false, fontSize:12}, {text: that.form.proprio.proprio_nom+' '+that.form.proprio.proprio_prenom, fontSize:12, bold: true, italics: true}, {text: ", Propriétaire d’immeuble,", italics: false, fontSize:12}]}]); 

		            bail.push([{text: [{text: "\nReprésenté par la société ", italics: false, fontSize:12}, {text: that.form.agence.agence_nom.toUpperCase(), fontSize:12, bold: true, italics: true}, {text: ", ayant son siège à: ", italics: false, fontSize:12}, {text: that.form.agence.agence_adresse+" "+that.form.agence.agence_ville+" "+that.form.agence.agence_pays+", ", italics: false, fontSize:12}]}]); 

		            bail.push([{text: "\nDésigné ci-après dénommé le bailleur.", fontSize:12, bold: false}]);

		            bail.push([{text: "\n\u200B\t\t\tEt d’autre part :\n", fontSize:12, bold: true}]);

		            // Info locataire
		            bail.push([{text: [{text: "\n "+that.form.locataire.locat_civilite+" ", italics: false, fontSize:12}, {text: that.form.locataire.locat_nom.toUpperCase()+" "+that.form.locataire.locat_prenom, fontSize:12, bold: true, italics: true}]}]);   
                    
                    bail.push([{text: [{text: "\nDate et lieu de naissance : ", italics: false, fontSize:12}, {text: that.format_date_FR(that.form.locataire.locat_date_naissance) +" à "+that.form.locataire.locat_pays_naissance, fontSize:12, bold: true, italics: true}]}]);   

                    bail.push([{text: [{text: "\nProfession : ", italics: false, fontSize:12}, {text: that.form.locataire.locat_profession, fontSize:12, bold: true, italics: true}]}]); 

                    bail.push([{text: [{text: "\nType de pièce d’identité : ", italics: false, fontSize:12}, {text: that.form.locataire.locat_type_piece, fontSize:12, bold: true, italics: true}, {text: "\u200B\t\t\t\tN° de la pièce d’identité : ", italics: false, fontSize:12}, {text: that.form.locataire.locat_numero_piece, fontSize:12, bold: true, italics: true}]}]);

                    bail.push([{text: [{text: "\nAdresse : ", italics: false, fontSize:12}, {text: that.form.locataire.locat_adresse+", "+that.form.locataire.locat_ville+", "+that.form.locataire.locat_pays, fontSize:12, bold: true, italics: true}]}]);  
                    
                    bail.push([{text: [{text: "\nDésigné ci-après dénommé le preneur. ", italics: false, fontSize:12}]}]); 

                    bail.push([{text: [{text: "\nIl a été convenu et arrêté ce qui suit : ", italics: true, fontSize:12}]}]);  

                    bail.push([{text: [{text: "\nPar les présentes, ", italics: false, fontSize:12}, {text: that.form.agence.agence_nom, italics: false, fontSize:12}, {text: " fait bail à usage "+(that.form.type_bail.toLowerCase()=='habitation'? 'd’':'')+that.form.type_bail.toLowerCase()+" et donne à loyer pour la durée ci-après indiquée ;", italics: false, fontSize:12}]}]);
		            
                    bail.push([{text: [{text: "\nA Monsieur ", italics: false, fontSize:12}, {text: that.form.locataire.locat_nom.toUpperCase()+" "+that.form.locataire.locat_prenom, bold: true, italics: true, fontSize:12}]}]);

                    bail.push([{text: [{text: "\nSoussignée qui accepte les locaux.", italics: false, fontSize:12}]}]);

                    bail.push([{text: "\nII. OBJET DU CONTRAT\n", fontSize:12, bold: true }]);

		            bail.push([{text: [{text: "\nLe présent contrat a pour objet la location de :\n\n", italics: false, fontSize:12}]}]);

		            for(var i=0; i<that.form.local.length; i++){
		            	var obj = that.form.local[i];
		            	bail.push([{ul: [
			                that.capitalizeFirstLetter(obj.local_type_local)+' ('+obj.local_type_location+')\n'
			            ], italics: false, bold: true, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

			            var salle_bain = "";

			            if(obj.local_salle_bain=='oui'){
			            	salle_bain = " (avec salle de bain)";
			            }else{
			            	salle_bain = " (sans salle de bain)";
			            }

			            bail.push([{text: [{text: "\n\u200B\t\t\tNombre de piéce: "+obj.local_nombre_piece, italics: false, fontSize:12}, {text: salle_bain, italics: false, fontSize:12}]}]);
			            if(obj.local_description){
			            	bail.push([{text: [{text: "\n\u200B\t\t\tDescription: ", italics: false, fontSize:12}]}]);
			            	bail.push([{text: [{text: "\u200B\t\t\t"+obj.local_description+"\n\n", italics: false, fontSize:12}]}]);
			            }
			            
			            
		            }

		            bail.push([{text: "\nIII. DATE DE PRISE D’EFFET ET DUREE DU CONTRAT\n", fontSize:12, bold: true }]);
		            var duree_contrat_mois = parseInt(that.form.duree) * 12;

		            bail.push([{text: [{text: "\n Le présent bail est consenti pour une Durée Déterminée de "+that.numberToLetter(duree_contrat_mois)+" mois ("+duree_contrat_mois+") mois. Prenant effet en date du ", italics: false, fontSize:12}, {text: that.format_date_FR(that.form.date_debut), italics: true, bold: true, fontSize:12}, {text: " et expirant le ", italics: true, fontSize:12}, {text: that.format_date_FR(that.form.date_fin), italics: true, bold: true, fontSize:12}, {text: " et renouvelable par tacite reconduction selon le respect des engagements.", italics: true, fontSize:12}]}]);

		            // Les préavis de résiliation à configurer dans préférences

		            bail.push([{text: [{text: "\nLe préavis de résiliation est de deux (02) mois pour le locataire et six (06) mois pour le propriétaire, chacune des parties pourra mettre fin au bail en avisant l’autre par lettre recommandée, avec avis de réception. Le présent bail est régi par les dispositions des articles 547 et suivants du COCC (Code des Obligations Civiles et Commerciales) et des textes qui le complètent.", italics: false, fontSize:12}]}]);


		            bail.push([{text: "\nIV- CLAUSES ET CONDITIONS\n", fontSize:12, bold: true }]);

		            bail.push([{text: [{text: "\nLe présent bail est fait aux clauses et conditions suivantes, que le preneur s’oblige à exécuter :", italics: false, fontSize:12}]}]);

		            bail.push([{text: "\n1) Etat des lieux\n\n", fontSize:12, bold: true }]);

		            bail.push([{ol: [
		                "Le bailleur s'engage à délivrer les locaux en bon état ; un état des lieux sera établi contradictoirement lors de la remise des clés au preneur, il sera annexé au contrat ;\n\n",
		                "Le preneur prendra les locaux dans l'état où ils se trouvent lors de son entrée en jouissance ;\n\n",
		                "Il sera tenu aux réparations locatives et le bailleur étant tenu aux grosses réparations ;\n\n",
		                "Il en jouira en bon père de famille, les entretiendra pendant la durée du bail et les rendra à sa sortie en bon état. Tous travaux d’embellissement ou d’amélioration demeurant acquis au bailleur s’il désire ;\n\n",
		                "Le preneur ne pourra faire aucun aménagement, aucune modification ou transformation dans l’état ou la disposition des locaux, sans l'autorisation préalable par écrit du bailleur ;",
		                "Le preneur ne pourra faire aucun aménagement, aucune modification ou transformation dans l’état ou la disposition des locaux, sans l'autorisation préalable par écrit du bailleur ;\n\n",

		                "Le bailleur conservera le droit d'exiger la remise des lieux louées dans leur état primitif aux frais du preneur à la fin du bail.\n\n"
		              
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            bail.push([{text: "\n2) Règlement Urbain\n\n", fontSize:12, bold: true }]);
                    
                    bail.push([{text: [{text: "\n Le preneur devra acquitter exactement ses contributions personnelles, de cel et autres et satisfaire à toutes les charges de voirie et d’hygiène, éclairage et autres, de ville et de police auxquelles les locataires sont ordinairement tenus.", italics: false, fontSize:12}]}]);

                    bail.push([{text: "\n3) Assurances\n", fontSize:12, bold: true }]);
                    
                    bail.push([{text: [{text: "\n Le preneur est tenu de faire assurer et de tenir constamment assurés, à compter de la conclusion du présent bail et cela pendant toute la durée de son occupation des locaux, contre les risques et les responsabilités de toute nature qu'il peut encourir au titre de la présente location (incendies, dégâts des eaux, bris de glaces, responsabilité-civile).", italics: false, fontSize:12}]}]);

                    bail.push([{text: [{text: "\n Il devra également faire assurer tous les objets mobiliers contenus dans les locaux loués contre l’incendie, les risques locatifs et le recours des voisins auprès d'une compagnie d'assurances notoirement solvable et agréée au Sénégal.", italics: false, fontSize:12}]}]);

                    bail.push([{text: "\n4) Cession ou sous location\n", fontSize:12, bold: true }]);
                    
                    bail.push([{text: [{text: "\n Le preneur ne jouit d’aucun droit à sous louer ni prêter les locaux, même temporairement, en totalité ou en partie, sauf accord express ou écrit du bailleur. Outre l’acceptation du bailleur, le prix de la sous location ne doit pas excéder le montant du loyer principal sous peine par le bailleur d’augmenter jusqu’à due occurrence le loyer ou de résiliation immédiate du bail principal.", italics: false, fontSize:12}]}]);

                    bail.push([{text: "\n5) Loyer\n", fontSize:12, bold: true }]);
                    
                    bail.push([{text: [{text: "\n Le Loyer est fixé selon l'expertise du calcul de la surface corrigée. \nLe présent bail est fait et accepté moyennant un loyer mensuel de "+that.numberToLetter(that.form.montant_ht)+" ("+that.form.montant_ht+") FCFA payable par mois. \nLe loyer est payable d'avance par terme mensuel ou trimestriel avant le 05 du mois échu.\n Le loyer est portable et non quérable. Il est expressément stipulé qu'en cas de retard dans le paiement des loyers, le locataire s'acquittera outre du montant du loyer, des frais d'huissier et d'actes d'avocat.", italics: false, fontSize:12}, ]}]);

                    bail.push([{text: "\n6) Charges\n", fontSize:12, bold: true }]);
                    
                    bail.push([{text: [{text: "\n Le preneur devra acquitter outre le loyer fixé ci-dessus, sa contribution aux taxes; prestations, charges.", italics: false, fontSize:12}]}]);

                    var TOM = ((parseInt(that.form.montant_ht.replaceAll(' ',''))*3.6)/100).toFixed(0);
                    var TVA = ((parseInt(that.form.montant_ht.replaceAll(' ',''))*18)/100).toFixed(0);
                    var LOYER = that.form.montant_ht.replaceAll(' ','');
                    var LOYER_TOTAL = parseInt(LOYER) + parseInt(TVA) + parseInt(TOM);

                    bail.push([{text: [{text: "\n Loyer Hors Taxe : "+that.form.montant_ht+" FCFA", italics: false, fontSize:13, bold: true}]}]);
                    bail.push([{text: [{text: "\n TOM (3,6%) : "+that.helper_separator_amount(TOM)+" FCFA", italics: false, fontSize:13, bold: true}]}]); // Taxe sur les ordures ménagéres
                    bail.push([{text: [{text: "\n Charges : ..", italics: false, fontSize:13, bold: true}]}]);
                    bail.push([{text: [{text: "\n TVA(18%) : "+that.helper_separator_amount(TVA)+" FCFA", italics: false, fontSize:13, bold: true}]}]);
                    bail.push([{text: [{text: "\n Soit un Loyer hors enregistrement de : "+that.numberToLetter(LOYER_TOTAL)+' '+that.helper_separator_amount(LOYER_TOTAL)+" FCFA", italics: false, fontSize:13, bold: true}]}]);

                    bail.push([{text: [{text: "\n En cas de paiement par chèques, le loyer ne pourra être considéré comme réglé qu'après son encaissement nonobstant la remise de la quittance. La clause résolutoire pourra être acquise au bailleur dans le cas où le chèque ne serait pas approvisionné.", italics: false, fontSize:12}]}]);

                    bail.push([{text: "\n7) Dont Quittance\n", fontSize:12, bold: true }]);
                    
                    bail.push([{text: [{text: "\n Tous les paiements auront lieu au siège de ", italics: false, fontSize:12}, {text: that.form.agence.agence_nom , italics: true, bold: true, fontSize:12}, {text: " et devront être effectués soit en espèces, soit par chèques bancaires.", italics: false, fontSize:12}]}]);

                    bail.push([{text: "\n8) Réajustement du loyer et des charges\n", fontSize:12, bold: true }]);
                    
                    bail.push([{text: [{text: "\n Le loyer pourrait être révisé tous les trois ans et variera en fonction de l'évolution des prix, notamment ceux du bâtiment.\n Les charges seront réajustées en fonction de la législation et de la réglementation en vigueur.", italics: false, fontSize:12}]}]);

                    bail.push([{text: "\n9) Clauses résolutoires :\n", fontSize:12, bold: true }]);
                    
                    bail.push([{ul: [
		                "A défaut de paiement du loyer à son échéance exacte ou à défaut de l'exécution d'une seule des conditions et clauses ci-dessus, le présent bail sera résilié de plein droit si bon semble au bailleur, trente (30) jours après une simple mise en demeure de payer ou d'exécuter, contenant déclaration par le bailleur de son intention d'user du bénéfice de la présente clause, sans qu'il soit nécessaire de remplir aucune formalité judiciaire.\n\n",
		                "Si le preneur refusait à vider les lieux, son expulsion aurait lieu sur simple ordonnance référé rendu par le juge compétent, le tout sans préjudice des loyers arriérés.\n\n",
		                "En cas de défaut de versement du dépôt de garantie, le présent bail sera resilié\n\n",
		                "En cas de trouble de voisinage constaté par une décision de justice, le présent bail sera resilié\n\n"
		              
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            bail.push([{text: "\n10) Clause de solidarité :\n", fontSize:12, bold: true }]);
                    
                    bail.push([{ul: [
		                "Pour l’exécution de toutes les obligations du présent contrat en cas de pluralité de locataires, il y aura solidarité et indivisibilité entre eux.\n\n",
		                "En cas de décès du preneur, il y aura solidarité et indivisibilité entre ses héritiers et représentants pour le paiement des loyers.\n\n"		              
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]);

		            bail.push([{text: "\n11) Autres conditions particulières (à définir par les parties) \n", fontSize:12, bold: true }]);
                    
                    bail.push([{ul: [
		                "Le locataire s’engage à payer d’avance ses loyers par trimestre à partir du deuxième mois de loyer à compter de la date d’entrée.\n\n"      
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]);

		            bail.push([{text: "\n12) Caution\n", fontSize:12, bold: true }]);
                    
                    bail.push([{ul: [
		                "\nLe preneur verse à titre de caution la somme de : "+that.numberToLetter(that.form.caution_mnt_ht)+" ("+that.helper_separator_amount(that.form.caution_mnt_ht)+") FCFA.\n Cette somme non productive d'intérêts ne sera remboursée au preneur qu'après :\n\n"      
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]);
                    
                    bail.push([{ul: [
		                "Avoir obtenu de la SEN’EAU et SENELEC des quitus attestant le paiement des quittances pendant la période d'occupation et jusqu'au jour du départ constaté.\n\n",
		                "Avoir apporté la feuille de résiliation du contrat auprès de ces compagnies\n\n",
		                "Avoir payé jusqu'au dernier mois\n\n",
		                "Avoir fait la remise en état complète des lieux\n\n",
		                "Avoir fait parvenir les clefs du logement\n\n"
		              
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            bail.push([{text: "\n13) Enregistrement\n\n", fontSize:12, bold: true }]);

		            bail.push([{ul: [
		                "Les frais et coûts des présents sont à la charge du locataire y compris les timbres et l'enregistrement. L'enregistrement est requis pour la durée du bail. Dans le cas de reconduction du présent bail, le preneur fait son affaire personnelle de requérir l'enregistrement des périodes à venir.\n\n"      
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]);

		            bail.push([{text: "\n14) Election de domicile\n\n", fontSize:12, bold: true }]);
                    
                    bail.push([{ul: [
		                "Pour l'exécution des présentes, les parties font élection de domicile :\n\n"      
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]);
                    
                    bail.push([{ul: [
		                "Le bailleur en sa demeure\n\n",
		                "Le locataire dans les lieux loués.\n"
		              
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            bail.push([{text: "\n15) Clause diplomatique\n", fontSize:12, bold: true }]);

		            bail.push([{text: [{text: "\n En cas de force majeure ou de cessation du preneur au Sénégal ou de son affectation dans une autre ville. Le preneur pourra résilier le présent bail moyennant un préavis écrit d'un mois, qu'il fera parvenir à ", italics: false, fontSize:12}, {text: that.form.agence.agence_nom , italics: true, bold: true, fontSize:12}, {text: " par lettre recommandée avec accusé de réception.", italics: false, fontSize:12}]}]);
               

                    var approuve = new Table([[{text: 'Fait en trois (3) exemplaires', fontSize: 12, noWrap: false , bold: false, alignment: 'left'},{text: 'Dakar, le '+ that.currentDateTime()+"\n\n", fontSize: 12, noWrap: false , bold: false, alignment: 'right'}]]).widths(['50%', '50%']).layout('noBorders').margin([0, 25, 0, 0]).end;

		            bail.push(approuve);

		            // Signatures

		            var signature = new Table([[{text: 'LE PRENEUR (Lu et approuvé)', fontSize: 12,decoration: 'underline', noWrap: false , bold: true, alignment: 'left'},{text: 'LE BAILLEUR', fontSize: 12,decoration: 'underline', noWrap: false , bold: true, alignment: 'right'}]]).widths(['50%', '50%']).layout('noBorders').margin([0, 50, 0, 0]).end;

		            bail.push(signature);

		           

		            bail.push([{text: [{text: "\n Pièces Jointes :\n", italics: false, fontSize:12}, {text: that.form.agence.agence_nom , italics: true, bold: true, fontSize:12}, {text: " État des lieux\nPièce d’identité du preneur", italics: true, fontSize:12}]}]); 

		            var header = new Table(entete).widths([60,'*',60]).layout('noBorders').margin([0, 0, 0, 0]).end;

		            
		            pdf.add(title_and_qrcode);
		            
		            pdf.add(
		                pdf.ln(1)
		            );

		            pdf.add(bail);


		            pdf.header(function(currentPage, pageCount) { 
		                return  {
		                        columns: [
		                            {   margin: [ 30, 20, 30, 0 ],
		                                table: {
		                                    widths: [ '20%','60%', '20%'],
		                                    margin: [0, 30, 0, 0],
		                                    body: [
		                                        [
		                                            { image: base64Img,

		                                                width: 80,
		                                                alignment: 'left'
		                                            },
		                                            text_entete,
		                                            {text: 'BAIL N°'+(that.form.id!= '' ? that.form.id.split("-")[1]:'XXXXXX'), alignment: "right", width: 50, lineHeight: 2, decoration: 'underline', bold: true, margin: [0, 35, 0, 0], color: '#b7ae7f'}
		                                        ]
		                                    ]
		                                },
		                                layout: 'noBorders'
		                            }
		                        ]
		                }
		            });

		            pdf.footer(function(currentPage, pageCount) { 
		                return  {
		                    margin: [30, 15, 30, 0],  // [L, T, R, B]
		                    //height: 100, 
		                    columns: [

		                        {alignment: "left", text: 'IMMO V1', width: 50, bold: true, lineHeight: 2,color: '#446da9', decoration: 'underline'}, 
		                        text_footer, 
		                        {text: currentPage.toString() + ' / ' + pageCount, alignment: "right", width: 50, lineHeight: 2, decoration: 'underline'}
		                    ]}; 
		            });

		            pdf.create().getDataUrl(function(file) { 

		                that.bail_previsualise = file;
		                that.name_file = nameFile; 

		                console.log("File>>>", file, "File PDF =", nameFile); 

		                if(generate_real_bail){
		                	console.log("Generate FIle");
		                	EventBus.$emit('SAVE_FILE_BAIL', { 
				               file_genered_save: file,
				               name_file_save: nameFile
				            });
		                }

		            })      

		        });
	        },
	        randomIntFromInterval(min, max) { // min and max included 
			  return Math.floor(Math.random() * (max - min + 1) + min)
			}
	    },
	    mounted() {
	        EventBus.$on('VIEW_PREV_BAIL', (event) => {
	            this.form = event.bail;
	            this.generatePdf();
	        });

	        EventBus.$on('GENERATED_BAIL', (event) => {
	            this.form = event.bail;
	            this.generatePdf(true);
	            this.generateFile=true;
	        });

	        
	    }
  }
</script>
