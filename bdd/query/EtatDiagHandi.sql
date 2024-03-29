SELECT  SUBSTRING(fdcRef.valeur,1,20),  
  COUNT(gc3.id_critere) moteur
					, COUNT(gc1.id_critere) audio
					, COUNT(gc4.id_critere) visu
					, COUNT(gc2.id_critere) cog
								 
					FROM spip_rubriques r 
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique 
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_article = a.id_article INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fda.id_donnee 
					   AND fd.id_form =59 
					INNER JOIN spip_forms_donnees_champs fdcRef ON fdcRef.id_donnee = fd.id_donnee 
					   AND fdcRef.champ = 'ligne_1'
					INNER JOIN spip_forms_donnees_champs fdcRep ON fdcRep.id_donnee = fd.id_donnee 
					   AND fdcRep.champ = 'mot_1'
					INNER JOIN spip_mots m ON m.id_mot = fdcRep.valeur AND m.titre='Non'
					
          LEFT JOIN gevu_solus.gevu_criteres gc1 ON gc1.ref = fdcRef.valeur AND gc1.handicateur_auditif > 0
          LEFT JOIN gevu_solus.gevu_criteres gc2 ON gc2.ref = fdcRef.valeur AND gc2.handicateur_cognitif > 0
          LEFT JOIN gevu_solus.gevu_criteres gc3 ON gc3.ref = fdcRef.valeur AND gc3.handicateur_moteur > 0
          LEFT JOIN gevu_solus.gevu_criteres gc4 ON gc4.ref = fdcRef.valeur AND gc4.handicateur_visuel > 0
          
				 WHERE r.id_rubrique IN(7304,7303,7154,7307,7306,7305,7310,7309,7308,7315,7314,7317,7316,7313,7320,7319,7322,7321,7318,7325,7324,7327,7326,7329,7328,7331,7330,7323,7334,7333,7336,7335,7332,7339,7338,7341,7340,7337,7344,7343,7346,7345,7342,7349,7348,7351,7350,7353,7352,7347,7367,7366,7369,7368,7371,7370,7365,7374,7373,7372,7377,7376,7375,7380,7379,7382,7381,7378,7385,7384,7387,7386,7383,7390,7389,7388,7393,7392,7395,7394,7396,7391,7402,7401,7400,7405,7404,7403,7409,7408,7414,7413,7407,7417,7416,7419,7418,7421,7420,7423,7422,7415,7426,7425,7428,7427,7424,7433,7432,7435,7434,7431,7438,7437,7440,7439,7442,7441,7436,7445,7444,7447,7446,7443,7450,7449,7452,7451,7448,7491,7490,7493,7492,7497,7496,7453,7458,7457,7461,7460,7459,7464,7463,7462,7467,7466,7469,7468,7465,7474,7473,7472,7477,7476,7479,7478,7475,7482,7481,7480,7486,7485,7488,7487,7484,7489,7506,7505,7597,7596,7500,7508,7507,7599,7598,7501,7510,7509,7601,7600,7502,7514,7513,7603,7602,7504,7517,7520,7519,7605,7604,7607,7606,7518,7527,7526,7609,7608,7521,7529,7528,7522,7531,7530,7612,7611,7523,7533,7532,7524,7535,7534,7614,7613,7525,7553,7552,7619,7618,7536,7555,7554,7622,7621,7537,7557,7556,7595,7594,7624,7623,7538,7559,7558,7626,7625,7539,7561,7560,7628,7627,7541,7563,7562,7591,7590,7635,7634,7542,7565,7564,7637,7636,7639,7638,7543,7567,7566,7544,7569,7568,7641,7640,7545,7571,7570,7546,7573,7572,7593,7592,7643,7642,7547,7575,7574,7645,7644,7647,7646,7548,7577,7576,7583,7582,7589,7588,7549,7579,7578,7587,7586,7550,7581,7580,7585,7584,7551,7631,7630,7633,7632,7629,7151) 
  group by fdcRef.valeur