<template>
	<div>
		<div class="modal fade fullscreenModal" id="previsualitionMandat" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="preVisual" aria-hidden="true">
                
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5  v-if="!generateFile" class="modal-title text-uppercase"><strong><span class="text-primary"><u>Prévisualisation</u></span> Mandat</strong></h5>
                        <h5  v-if="generateFile" class="modal-title text-uppercase"><strong><span class="text-primary"><u>Mandat</u></span> Final</strong></h5>
                        <button type="button" class="close" ref="closePopupPrevisual" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-body modalinfo">
                          <embed :src="mandat_previsualise" frameborder="0" width="100%" height="450px">    
                    </div>
                     <div class="modal-footer justify-content-center">
                        <button  v-if="!generateFile" type="button" class="btn btn-success btn-lg" @click="validation_mandat()">Valider</button>
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
	           mandat_previsualise: null,
	           name_file: '',
	           form: {},
	           generateFile: false
	        }
	    },
	    methods: {
	        validation_mandat(){
	        	EventBus.$emit('SAVE_MANDAT', { 
	               file_genered: this.mandat_previsualise,
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
	       	generatePdf(generate_real_mandat=false){

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

		            var nameFile = "mandat-gerance-"+that.form.agence.agence_nom.replace(/[^a-z0-9]/gi, '').toLowerCase()+"-"+that.form.proprio.proprio_prenom.replace(/[^a-z0-9]/gi, '').toLowerCase()+"-"+that.form.proprio.proprio_nom.replace(/[^a-z0-9]/gi, '').toLowerCase()+"-"+that.randomIntFromInterval(0, 9999)+".pdf";

		            var logo_entete=[], entete=[];
		            logo_entete.push({stack: [{ image: base64Img, width: 60}]}); 

		            var text_entete = [[{text: that.form.agence.agence_nom.toUpperCase(), fontSize: 25, color: '#446da9', noWrap: true , bold: true, margin:[0, 20, 0, 0], alignment: 'left'}], [{text: that.form.agence.agence_activite, fontSize: 11, noWrap: false , bold: true, alignment: 'left'}]];  

		            entete.push([logo_entete, text_entete, new QR(location.origin+"/assets/mandats/"+nameFile).fit(60).alignment('right').end]); 

		            /*Footer settings*/

		            var text_footer = [{text: that.form.agence.agence_nom.toUpperCase()+' '+that.form.agence.agence_ville +' ('+that.form.agence.agence_pays+') '+that.form.agence.agence_adresse+' Téléphone:'+that.form.agence.agence_ind1+' '+that.form.agence.agence_tel1+' NINEA '+that.form.agence.agence_ninea+' '+that.form.agence.agence_email, fontSize: 11, color: '#7e7e7e', noWrap: false , bold: false, alignment: 'center', width: '*'}];


		            /* Content */

		            var title = new Table([[{text: 'MANDAT DE GERANCE', fontSize: 23, noWrap: false , bold: true, alignment: 'center'}]]).widths('100%').margin([0, 5, 0, 0]).end;

		            var title_and_qrcode = new Table([[title, new QR(location.origin+"/assets/mandats/"+nameFile).fit(75).alignment('right').end]]).widths(['*', 80]).layout('noBorders').margin([0, 0, 0, 0]).end;

		           

		            // Le mandat
		            var mandat = [];

		            mandat.push([{text: "Entre les soussignés ci-après dénommés « LE MANDANT » et « LE MANDATAIRE »\n", fontSize:12, bold: false }]);

		            mandat.push([{text: "\n1- LE MANDANT « Le propriétaire »", decoration: 'underline', fontSize:12, bold: false}]);

		            mandat.push([{text: [{text: "\nMonsieur : ", italics: true, fontSize:12}, {text: that.form.proprio.proprio_nom+' '+that.form.proprio.proprio_prenom, fontSize:12, bold: true}]}]);

		            mandat.push([{text: [{text: "\nDate et lieu de naissance : ", italics: true, fontSize:12}, {text: that.form.proprio.proprio_date_naissance+" à "+that.form.proprio.proprio_ville_naissance+" ("+that.form.proprio.proprio_pays_naissance+")", fontSize:12, bold: true}]}]);

		            mandat.push([{text: [{text: "\nType de piéce : ", italics: true, fontSize:12}, {text: that.form.proprio.proprio_type_piece, fontSize:12, bold: true}]}]);

		            mandat.push([{text: [{text: "\nN° de la pièce d’identité : ", italics: true, fontSize:12}, {text: that.form.proprio.proprio_numero_piece, fontSize:12, bold: true},{text: "\t\t\t\t\tNationalité : ", italics: true, fontSize:12}, {text: that.form.proprio.proprio_nationalite.toUpperCase(), fontSize:12, bold: true}]}]);

		            mandat.push([{text: [{text: "\nAdresse : ", italics: true, fontSize:12}, {text: that.form.proprio.proprio_adresse, fontSize:12, bold: true}]}]);

		            mandat.push([{text: [{text: "\nProfession : ", italics: true, fontSize:12}, {text: that.form.proprio.proprio_profession, fontSize:12, bold: true}]}]);
                    
                    if(that.form.position){
                    	mandat.push([{text: "\n Représenté par "+that.form.position.repr_civilite+' '+that.form.position.repr_nom+' '+that.form.position.repr_prenom+', '+that.form.position.repr_type_piece+' ('+that.form.position.repr_numero_piece+')', fontSize:12, bold: false}]); 
                    }
		            //Représentant des héritiers du défunt SACKO propriétaire de l’immeuble. 

		            // Le mandataire
		            mandat.push([{text: "\n2- LE MANDATAIRE", decoration: 'underline', fontSize:12, bold: false}]);  

		            mandat.push([{text: [{text: '\n«'+that.form.agence.agence_nom.toUpperCase()+'» SARL, ', italics: true, fontSize:12, bold: true}, {text: ' dont le siège social est situé à '+that.form.agence.agence_adresse+", immatriculée au registre de commerce ", fontSize:12, bold: false}, {text: that.form.agence.agence_ninea+".", fontSize:12, bold: true}]}]);

		            mandat.push([{text: [{text: "\nReprésenté par Monsieur ", fontSize:12, bold: false}, {text: that.form.pers.pers_nom+" "+that.form.pers.pers_prenom, fontSize:12, bold: true}, {text: " en sa qualité de Gérant.", italics: true, fontSize:12, bold: false}]}]);

		            // Le mandat
		            mandat.push([{text: "\nIL A ÉTÉ FAIT ET CONVENU CE QUI SUIT :\n", fontSize:12, bold: true }]);

		            mandat.push([{text: [{text: '\nLe mandant déclare par les présentes donner mandat à ', italics: false, fontSize:12}, {text: that.form.agence.agence_nom.toUpperCase(), italics: true, fontSize:12, bold: false}, {text: ' qui accepte d’administrer le bien situé : ', italics: false, fontSize:12}, {text: that.form.bien.bien_adresse.toUpperCase()+', N° '+that.form.bien.bien_numero+', '+that.form.bien.bien_ville.toUpperCase()+', '+that.form.bien.bien_pays.toUpperCase()+(that.form.bien.bien_nom!='' && that.form.bien.bien_nom!=null? " ("+that.form.bien.bien_nom+")":'')+'.', italics: false, fontSize:12, bold: true}]}]);

		            mandat.push([{text: [{text: '\nLequel est reçu par ', italics: false, fontSize:12}, {text: that.form.agence.agence_nom.toUpperCase(), italics: true, fontSize:12, bold: false,  alignment: 'justify'},{text: ' dans son état actuel au moment de la remise des clefs, tant dans son ensemble que dans les détails, et dont toute erreur ou malfaçon ne peut être prise en charge que par le propriétaire qui voit ainsi sa seule responsabilité engagée. ', italics: false, fontSize:12,  alignment: 'justify'} ]}]);

		            mandat.push([{text: [{text: '\nA cet effet, le propriétaire donne par le présent contrat mandat exprès de gérer et d’administrer l’immeuble, ledit mandat s’étendant à tous les actes d’une gestion normale et comprenant, sans que cette énumération soit limitative, les pouvoirs ci-dessous.', italics: false, fontSize:12,  alignment: 'justify'} ]}]);

		            // Le pouvoir
		            mandat.push([{text: "\n3- POUVOIRS", decoration: 'underline', fontSize:12, bold: false}]);  

		            mandat.push([{text: [{text: '\nLe mandant donne pouvoir au mandataire :', italics: false, fontSize:12} ]}]);

		            mandat.push([{ol: [
		                'D’effectuer toutes les démarches en vue d’assurer la location des locaux de l’immeuble aux tiers, pour la durée et au prix, charges et conditions qu’il jugera convenables en conformité aux lois locatives ;\n\n',
		                'Donner ou accepter tout congé, signer pour le compte du propriétaire toutes cessions des baux, dès la signature de ce contrat ;\n\n',
		                'Faire dresser tous états des lieux d’entrée et de sortie, exiger des réparations à la charge des locataires ;\n\n',
		                 'Effectuer tous encaissements et paiements que comporte la gestion de l’immeuble (loyers, frais accessoires et autres obligations découlant des baux à loyer) en donnant des quittances ou décharges valables ;\n\n',
		                'Poursuivre les locataires en retard, porter plainte pour les divergences avec les locataires ou toutes autres dispositions nécessaires à une gérance ordonnée et rigoureuse ; confier le dossier à un avocat et expulser les locataires récalcitrants ;\n\n',
		                'Représenter le propriétaire dans la conclusion des contrats d’assurances, d’abonnements d’eau, de gaz et d’électricité ;\n\n',
		                'Payer tous frais ou impôts quelconques relatifs à l’immeuble ;\n\n',
		                'Procéder à tous travaux de réparations ou rénovation nécessaires à l’immeuble à cette fin passer tous devis, marchés et contrats ; faire tout appel d’offre avec tous architectes, entrepreneurs, ouvriers, administrations, société et payer de tous mémoires les factures. Cependant, pour toutes grosses œuvres, l’accord du propriétaire sera demandé avec devis comparatif.\n\n'
		            ], italics: false,  alignment: 'justify', margin:[20, 0, 0, 0], fontSize:12} ]);

		            // CLAUSES ET CONDITIONS
		            mandat.push([{text: "\n4- CLAUSES ET CONDITIONS\n\n", decoration: 'underline', fontSize:12, bold: false}]); 

		            mandat.push([{ol: [
		                'Le propriétaire ne doit prendre aucun engagement avec les locataires au sujet des baux à loyer, des réparations ou l’utilisation de locaux, ni encaisser les loyers sans avoir informé préalablement le bailleur.\n\n',
		                'Le bailleur doit gérer les cautions des locataires durant toute la durée du mandat signé entre les deux parties. A la résiliation de celui-ci le bailleur doit restituer l’intégralité des cautions émises aux propriétaires qui, à son tour, remet une pièce justificative attestant la perception du montant de la part de son ex bailleur ; ainsi, des lettres seront adressées aux locataires informant de la résiliation des contrats qui les liés aux bailleurs.\n\n',
		                'Les documents comptables liés aux encaissements et dépenses seront remis au plus tard le 20 du mois en cours au propriétaire pour l’option du versement est mensuel, le 20 du 4 e mois pour l’option du versement trimestriel. Le propriétaire doit faire des observations éventuelles par écrit ou tout autre moyen avec des pièces justificatifs à l’appuis. Les comptes et justificatifs doivent être constamment à la disposition du propriétaire. Le bailleur qui peut également, en cas d’erreur comptable, informer et expliquer la nature au propriétaire et soustraire l’équivalent du montant au prochain mois.\n\n',
		                'Si le propriétaire utilise un crédit au près du bailleur, il lui cède en même temps les loyers à encaisser jusqu’au règlement définitif du montant en question\n\n',
		                'Pour les locataires qui paient tous les loyers, frais, contributions et autres versements résultant des baux via un virement bancaire, ils devront le faire sur un compte au nom du bailleur qui les désigne.',
		                'Le versement des loyers sera fait selon les convenances du propriétaire soit mensuellement ou trimestriellement …\n\n',
		                'Le bailleur n’assume aucune responsabilité pour les dégâts qui pourraient survenir au logement par la faute ou la négligence des réparations d’ordre non locatif que le propriétaire gère lui-même et ne répond pas du préjudice qui pourrait être causé de ce fait à ce dernier. La responsabilité du bailleur est engagée en cas de poursuite judiciaire entamée par le locataire. Pour des intervention urgente de réparation ou d’installation, le bailleur, s’il n’arrive pas à joindre le propriétaire pour des raisons de sécurité des locataires et de leurs biens, devra intervenir immédiatement sans son avis.\n\n',
		                'L’agence doit effectuer les diligences liées au contrat de mandat et à travailler dans l’intérêt du propriétaire, s’occupe d’une location pleine et durable. En revanche, la responsabilité du bailleur ne pourra être engagée en cas de pertes survenues à la suite du non-paiement des locations, des vacances de logements, pour autant que le dommage soit survenu sans sa faute et qu’elle est vouée tous ses soins à l’exécution de son mandat.\n\n' 
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            // CONDITION FINANCIAIRE

		            mandat.push([{text: "\nCONDITIONS FINANCIÈRES DU CONTRAT\n\n", decoration: 'underline', fontSize:12, bold: false}]);

		            mandat.push([{ul: [
		                'Les honoraires de gestion sont de '+that.numberToLetter(that.form.honoraire_gestion)+' pour cent ('+that.form.honoraire_gestion+'%) sur les sommes collectées pour chaque mois.\n\n',
		                'Les frais et honoraires résultant des consultations, poursuites, expulsions sont à la charge du propriétaire, pour autant que ceci ne puisse pas être récupéré au près du locataire.\n\n',
		                'Les frais administratifs, les taxes et les frais de procédure éventuels sont à la charge du propriétaire.\n\n',
		                'La surveillance des petits travaux de réparation et d’entretien de l’immeuble est comprise dans les honoraires de gestion. Toutes prestations spéciales sortant du cadre de la gestion normale seront facturées au taux de 5% du montant total des travaux mis en œuvre. Il en sera notamment ainsi pour la surveillance, la conduite ou la réception de travaux de construction, de reconstruction, d’agrandissement, de transformations ou de remises à neuf importantes ou pour des négociations spéciales telles celles relatives à des impôts etc.\n\n',
		                
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]);   

		            // DUREE DU CONTRAT
		            mandat.push([{text: "\nDUREE DU CONTRAT\n\n", decoration: 'underline', fontSize:12, bold: false}]);

		            mandat.push([{ul: [
		                'Le présent contrat est conclu pour une de '+that.numberToLetter(that.form.duree)+' ('+that.form.duree+') ans et il est renouvelable par tacite reconduction.\n\n',
		                'Le préavis de résiliation est de  '+that.numberToLetter(that.form.preavis_proprio)+' ('+that.form.preavis_proprio+') mois pour le propriétaire et '+that.numberToLetter(that.form.preavis_mandataire)+' ('+that.form.preavis_mandataire+') mois pour le bailleur. Il pourra y être mis fin à tout moment moyennant préavis de trois (3) mois de part et d’autre.\n\n',
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            // CLAUSES PARTICULIERES
		            mandat.push([{text: "\nCLAUSES PARTICULIERES\n\n", decoration: 'underline', fontSize:12, bold: false}]);

		            mandat.push([{ul: [
		                'Il est expressément convenu qu’en cas de litige tous les frais se découlant seront remboursés sur pièces justificatives par la partie perdante.\n\n'
		            ], italics: false, margin:[20, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            // CONFIDENTIALITE
		            mandat.push([{text: "\nCONFIDENTIALITE\n\n", decoration: 'underline', fontSize:12, bold: false}]);

		            mandat.push([{text: "Les parties s'engagent à garder une confidentialité absolue sur tout ce qui a trait aux obligations contractuelles qui ont eu lieu entre elles, et aux négociations auquel elles sont finalement parvenues.\n\n" , italics: false, margin:[0, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 


		            // DROIT APPLICABLE
		            mandat.push([{text: "\nDROIT APPLICABLE\n\n", decoration: 'underline', fontSize:12, bold: false}]);

		            mandat.push([{text: "Le présent protocole est soumis et interprété conformément au Droit sénégalais.\n\n" , italics: false, margin:[0, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 
		            mandat.push([{text: "Tout litige auquel pourraient donner lieu l’exécution, l’interprétation ou la validité du présent protocole sera, sous réserve des compétences d’attribution d’ordre public, de la compétence exclusive du Tribunal de Grand Instance de DAKAR.\n\n" , italics: false, margin:[0, 0, 0, 0], alignment: 'justify', fontSize:12} ]); 

		            mandat.push([{text: "Etabli dans les locaux du mandataire en deux exemplaires originaux\n\n" , italics: false, margin:[0, 10 ,0, 0], alignment: 'justify', fontSize:12} ]);

		            mandat.push([{text: "Un (1) pour le propriétaire\n\n" , italics: true, margin:[0, 0 ,0, 0], alignment: 'justify', fontSize:12} ]); 
		            mandat.push([{text: "Un (1) pour "+ that.form.agence.agence_nom.toUpperCase()+"\n\n" , italics: true, margin:[0, 0 ,0, 0], alignment: 'justify', fontSize:12} ]); 

		            mandat.push([{text: "Fait à Dakar, le "+ that.currentDateTime()+"\n\n" , italics: true, margin:[0, 10 ,0, 0], alignment: 'justify', fontSize:12} ]); 

		            // Signatures

		            var signature = new Table([[{text: 'LE PROPRIÉTAIRE', fontSize: 12,decoration: 'underline', noWrap: false , bold: true, alignment: 'left'},{text: 'LE GÉRANT', fontSize: 12,decoration: 'underline', noWrap: false , bold: true, alignment: 'right'}]]).widths(['50%', '50%']).layout('noBorders').margin([0, 15, 0, 0]).end;

		            mandat.push(signature);

		            // Lu et approuvé

		            var approuve = new Table([[{text: 'Lu et approuvé', fontSize: 12, noWrap: false , bold: false, alignment: 'left'},{text: 'Lu et approuvé', fontSize: 12, noWrap: false , bold: false, alignment: 'right'}]]).widths(['50%', '50%']).layout('noBorders').margin([0, 25, 0, 0]).end;

		            mandat.push(approuve);

		            mandat.push([{text: "*Signatures précédées des mentions manuscrites : « Lu et Approuvé »" , italics: true, margin:[0, 15 ,0, 0], alignment: 'justify', fontSize:12} ]); 

		            var header = new Table(entete).widths([60,'*',60]).layout('noBorders').margin([0, 0, 0, 0]).end;

		            
		            pdf.add(title_and_qrcode);
		            
		            pdf.add(
		                pdf.ln(1)
		            );

		            pdf.add(mandat);


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
		                                            {text: 'MANDAT N°'+(that.form.id!= '' ? that.form.id.split("-")[1]:'XXXXXX'), alignment: "right", width: 50, lineHeight: 2, decoration: 'underline', bold: true, margin: [0, 35, 0, 0], color: '#b7ae7f'}
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

		                that.mandat_previsualise = file;
		                that.name_file = nameFile; 

		                console.log("File>>>", file, "File PDF =", nameFile); 

		                if(generate_real_mandat){
		                	console.log("Generate FIle");
		                	EventBus.$emit('SAVE_FILE_MANDAT', { 
				               file_genered_save: file,
				               name_file_save: nameFile
				            });
		                }

		            })
		            //pdf.create().download();         

		        });
	        },
	        randomIntFromInterval(min, max) { // min and max included 
			  return Math.floor(Math.random() * (max - min + 1) + min)
			}
	    },
	    mounted() {
	        EventBus.$on('VIEW_PREV_MANDAT_DE_GERANCE', (event) => {
	            this.form = event.mandat;
	            this.generatePdf();
	        });

	        EventBus.$on('GENERATED_MANDAT_DE_GERANCE', (event) => {
	            this.form = event.mandat;
	            this.generatePdf(true);
	            this.generateFile=true;
	        });

	        
	    }
  }
</script>
