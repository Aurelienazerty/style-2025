<?php
require('fontawesome.php');

class template_site extends fontawesome {

  function __construct($id) {
		parent::__construct($id);
	}

	function header($headerLan = "", $myUser) {
		
		$recherche = '';
		if (isset($GLOBALS['recherche'])) {
			$recherche = $GLOBALS['recherche'];
		}
		
		$menuUser = $this->_menuConnexion($myUser);
		$menuUser = str_replace('</ul><br />', '</ul>', $menuUser);
		
		$header = '
			<!-- Header principal -->
			<header>
			
				<div class="header-left">
					<a href="/">
						<img src="/images/divers/logo-ours.png" alt="Logo Team-Azerty" class="logo">
					</a>
					<div class="site-title">
						<h1><a href="/">Team-Azerty</a></h1>
						<p class="slogan">Jeux vidéo en réseau</p>
					</div>
				</div>

				<div class="header-right">
					<div class="menu-hamburger">
						<button class="menu-toggle" aria-label="Menu" aria-expanded="false">
							<span class="hamburger-icon">
								<span class="bar"></span>
								<span class="bar"></span>
								<span class="bar"></span>
							</span>
						</button>
					</div>
					<div class="search-zone">
						<button class="search-toggle" aria-label="Recherche">🔍</button>
						<!-- Champ de recherche caché -->
						<div class="search-bar" style="display: none;">
							<form action="/html/recherche/" method="post" id="search" class="awi-search">
								<input type="text" placeholder="Recherche..." value="' . $recherche . '" name="keyword" id="keyword">
								<button type="submit" class="search-submit" aria-label="Lancer la recherche">
									<span class="fa-solid fa-magnifying-glass"></span>
								</button>
								<button type="button" class="search-close" aria-label="Fermer la recherche">
									<span class="fa-solid fa-xmark"></span>
								</button>
							</form>
						</div>
					</div>
					<div class="user-menu">
						<button class="user-toggle" aria-label="Utilisateur">👤</button>
						<div class="user-dropdown">
							' . $menuUser . '
						</div>
					</div>
				</div>
				
				<nav class="main-nav in-header" aria-label="Menu principal">
					' . $this->menu($myUser) . '
				</nav>
				
			</header>
		';
		
		/** On va déplacer l'icone de l'utilisateur */
		$dom = new DOMDocument();
		libxml_use_internal_errors(true); // Pour éviter les warnings sur le HTML incomplet
		$dom->loadHTML('<?xml encoding="utf-8" ?>' . $header);

		// Trouver le <a id="avatardUser">
		$avatarLink = $dom->getElementById('avatardUser');

		// Trouver le bouton .user-toggle
		$xpath = new DOMXPath($dom);
		$userToggle = $xpath->query('//button[contains(@class,"user-toggle")]')->item(0);

		if ($avatarLink && $userToggle) {
				// Cloner le lien avatar
				$avatarClone = $avatarLink->cloneNode(true);
				$avatarClone->setAttribute('class', trim($avatarClone->getAttribute('class') . ' user-toggle'));
				$userToggle->parentNode->replaceChild($avatarClone, $userToggle);
				$avatarLink->parentNode->removeChild($avatarLink);
		}
		
		return $dom->saveHTML();
	}

	function menuGauche($myUser, $titre) {
		$this->_initMenuGauche($myUser);

		$affichage_sondage = "";
		if (idActiveSondage() != -1) {
			$affichage_sondage = '
				<div class="card">
					<h3>Sondage</h3>
					' . afficherSondage(idActiveSondage(), $myUser) . '
				</div>';
		}

		//Avant il y avait une vérification de la présence de LAN. Mais depuis, j'en ajoute systématiquement après la LAN'Oween.
		return '
			<div class="card">
				<h3>Dernières actualités</h3>
				' . $this->lastNews . '
			</div>
			<div class="card">
				<h3>Les prochaines LAN</h3>
				' . $this->futuresLan . '
			</div>
			' . $affichage_sondage . '
			' . $this->_menuApropos() . '
			' . $this->publicités() . '
			' . $this->statistiques() . '
		';
	}

	protected function _menuApropos():string {
		return '
			<div class="card">
				<h3>À propos</h3>
				<ul>
					<li><a href="https://www.team-azerty.com/html/apropos/presentation.php" class="element_title" rel="nofollow" title="Présentation de notre assocation">Qui nous sommes ?</a></li>
					<li><a href="https://www.team-azerty.com/html/apropos/infos_legales.php" class="element_title" title="Voir les mentions légales">Mentions légales</a></li>
					<li><a href="https://www.team-azerty.com/html/apropos/revue_de_presse.php" class="element_title" rel="nofollow" title="Voir la revue de presse">Revue de presse</a></li>
					<li><a href="https://www.team-azerty.com/html/mail_webmaster.php" class="element_title" rel="nofollow" title="Formulaire de contact">Contact</a></li>
				</ul>
			</div>
		';
	}

	function afficher($myUser, $body, $titre, $navigation = array(), $meta = array()) {
		$body = $this->insertIconInListItems($body);
	  if (is_array($navigation)) {
	      $navigation = $this->filAriane($navigation);
	  }
	  if (defined('SEE_PROGRESS')) {
	      $progress = '<progress value="0"></progress>';
	      $conteneur = 'article';
	  } else {
	      $progress = "";
	      $conteneur = 'section';
	  }
		
		$admin = "";
		if ($GLOBALS['myUser']->deLaTeam()) {
			$admin .= '####ADMIN###';
		}
	  
	  $spreadFirefox = "";
	  if (!$myUser->isCrawler() && preg_match("/Firefox/i", $myUser->getObjetPhpBB()->browser) == 0) {
	      $spreadFirefox = "
	          <br />
	          <div class='card card--info' id='spreadfirefox'>
	              Utilisez un navigateur moderne qui respecte votre vie privée : 
	              <a href='https://www.mozilla.org/'>Téléchargez Firefox</a>
	          </div>
	      ";
	  }
	  $tabLANAVenir_entete = lanAVenir($myUser);
	  if (sizeof($tabLANAVenir_entete) > 0) {
	      $maLAN = array_shift($tabLANAVenir_entete);
	      $debutLan = $maLAN->date_debut("%d");
	      $finLan = $maLAN->date_fin("%d");
	      $moisAnneeLan = $maLAN->date_debut("%B %Y");
	      $headerLan = '
	      <h3 class="awi-prochainelan" data-left="61%">
	          <a href="/html/lan/fiche_lan.php?mode=presentation&amp;id=' . $maLAN->id() . '&amp;titre=' . xtTraiter($maLAN->nom()) . '">
	          ' . $maLAN->nom() . ' ' . $maLAN->lieu()->entete() . ' du ' . $debutLan . ' au ' . $finLan . ' ' . $moisAnneeLan . '
	          </a>
	      </h3>';
	  } else {
	      $headerLan = '';
	  }
	  if (!empty($navigation)) {
	      $navigation = '<nav class="breadcrumb" aria-label="Fil d\'Ariane">' . $navigation . '</nav>';
	  }
	  
	  $affichage_php = '
	      ' . $this->header($headerLan, $myUser) . '
	      <!-- Fil d\'Ariane -->
	      ' . $navigation . '
	      <!-- Contenu principal -->
	      <main>
	          <' . $conteneur . ' class="main-content">
	              ' . $this->processingContent($body) . '
	              ' . $spreadFirefox . '
	          </' . $conteneur . '>
	          <!-- Menu latéral sous forme de cartes -->
	          <aside class="sidebar">
	              ' . $this->menuGauche($myUser, $titre) . '
								' . $admin . '
	          </aside>
	      </main>
	      ' . $progress . '
	      ' . $this->footer() . '
	  ';
	  
	  if (defined('SEE_PROGRESS')) {
	      $this->scripts .= "
	      head.ready(function() {
	          function calculProgress() {
	              var winHeight = $(window).height(), 
	              docHeight = $('.site_content').height(),
	              progressBar = $('progress'),
	              max, value;
	              
	              /* Set the max scrollable area */
	              max = docHeight - winHeight;
	              progressBar.attr('max', max);
	          }
	          calculProgress();
	          $(window).on('scroll', function(){
	              value = $(window).scrollTop() - $('.site_content').offset().top;
	              if (value < 0) {
	                  value = 0;
	              }
	              var maxProgressBar = $('progress').attr('max');
	              if (value > maxProgressBar) {
	                  value = maxProgressBar;
	              }
	              $('progress').attr('value', value);
	          });
	          
	          $(window).on('orientationchange', function() {
	              calculProgress();
	          });
	          $(window).on('resize', function() {
	              calculProgress();
	          });
	      });
	      ";
	  }
		
		/** Fonctionnement via str_replace à la fin pour avoir les calculs finaux */
		$admin = "";
		if ($GLOBALS['myUser']->deLaTeam()) {
			$admin .= '<span class="droite centre small card card--info" style="margin-top: -0.85em;padding: var(--space-xs);">';
			$admin .= $this->mesures();
			if ($GLOBALS['myUser']->staff()) {
				$admin .= '/ Bdd: ' . hote;
			}
			if (isset($_SESSION['isMobile']) && $_SESSION['isMobile']) {
				$admin .= '<br>Mode mobile';
			} else {
				$admin .= '<br>Mode Bureau';
			}
			
			$admin .= '</span>';
		}
		
		$affichage_php = str_replace('####ADMIN###', $admin, $affichage_php);
		
	  return parent::_afficher($affichage_php, $titre, $meta);
	}

	function footer() {
		return '
			<footer>
				<a href="/rss-site-contenu.xml" title="Flux RSS actualité" rel="nofollow"><i class="fa-solid fa-rss"></i></a>
				<a href="https://bsky.app/profile/team-azerty.com" title="Nous suivre sur Bluesky Social" rel="nofollow"><i class="fa-brands fa-bluesky"></i></a>
				<a href="https://www.facebook.com/TeamAzerty" title="Page Facebook de l&#39;association" rel="nofollow"><i class="fa-brands fa-facebook-f"></i></a>
				<a href="https://www.instagram.com/assoteamazerty/" title="Page Instagram de l&#39;association" rel="nofollow"><i class="fa-brands fa-instagram"></i></a>
				<a href="/forum/" title="Forum de discussion de l&#39;association" rel="publisher"><i class="fa-solid fa-comments"></i></a>
				<a href="https://discord.gg/sfzVCQy" title="Serveur discord de l&#39;association" rel="publisher"><i class="fa-brands fa-discord"></i></a>
				<a href="https://www.paypal.me/azerty/" title="Faire un don à l&#39;association" rel="publisher"><i class="fa-solid fa-hand-holding-heart"></i></a>
			</footer>
		';
	}
	
	function scriptsJS() {
		return array(
			'all' => array(
				"./scripts/" . $this->dir_js . "/menu.js",
				"./scripts/" . $this->dir_js . "/recherche.js",
				"./scripts/" . $this->dir_js . "/menu-user.js",
			),
			'mobile'	=> array(
				"./scripts/" . $this->dir_js . "/menu-responsive.js",
			)
		);
	}
	
	function _processFluentIndex(&$xpath, &$dom) {
		// Trouve le blockIndex
		$blockIndex = $xpath->query('//*[@id="blockIndex"]')->item(0);
		if ($blockIndex) {
				// Ajoute la classe block-index
				$blockIndex->setAttribute('class', trim(($blockIndex->getAttribute('class') ?: '') . ' block-index'));

				// Liste des IDs à extraire et leur classe
				$cards = [
						'encartNewsIndex'      => 'card',
						'encartLanIndex'       => 'card',
						'carouselLan'          => 'card cacherMobile',
						'randomCR'             => 'card',
						'carouselLanPublique'  => 'card--wide cacherMobile',
						'encartForum'          => 'card--wide'
				];

				// On extrait les blocs
				$nodes = [];
				foreach ($cards as $id => $class) {
						$node = $xpath->query('//*[@id="'.$id.'"]')->item(0);
						if ($node) {
								$node->setAttribute('class', $class);
								$nodes[$id] = $node->cloneNode(true);
						}
				}

				// Supprime tous les enfants de blockIndex
				while ($blockIndex->hasChildNodes()) {
						$blockIndex->removeChild($blockIndex->firstChild);
				}

				// Ajoute les blocs dans la nouvelle structure
				if (isset($nodes['encartNewsIndex'])) {
						$blockIndex->appendChild($nodes['encartNewsIndex']);
				}

				// Colonne de droite imbriquée
				if (isset($nodes['encartLanIndex']) || isset($nodes['carouselLan'])) {
						$colDroite = $dom->createElement('div');
						$colDroite->setAttribute('class', 'col-droite');
						if (isset($nodes['encartLanIndex'])) {
								$colDroite->appendChild($nodes['encartLanIndex']);
						}
						if (isset($nodes['carouselLan'])) {
								$colDroite->appendChild($nodes['carouselLan']);
						}
						$blockIndex->appendChild($colDroite);
				}

				if (isset($nodes['randomCR'])) {
						$blockIndex->appendChild($nodes['randomCR']);
				}
				if (isset($nodes['carouselLanPublique'])) {
						$blockIndex->appendChild($nodes['carouselLanPublique']);
				}
				if (isset($nodes['encartForum'])) {
						$blockIndex->appendChild($nodes['encartForum']);
				}
		}
	}
	
	function _processFluentPronofoot(&$xpath, &$dom) {
		// === TRANSFORMATION DE #informationProno EN FLUENT2 CARD ===
    $infoProno = $xpath->query('//*[@id="informationProno"]')->item(0);
    if ($infoProno) {
        // Recherche le titre (ex: "Prochaines grilles:") et la liste
        $titre = '';
        $liste = null;

        // Recherche le h1 (titre)
        foreach ($infoProno->getElementsByTagName('h1') as $h1) {
            $titre = $h1->textContent;
            break;
        }
        // Recherche le ul (liste)
        foreach ($infoProno->getElementsByTagName('ul') as $ul) {
            $liste = $ul;
            break;
        }

        // Création de la nouvelle structure
        $aside = $dom->createElement('aside');
        $aside->setAttribute('class', 'fluent-card fluent-info');

        // Header
        $header = $dom->createElement('div');
        $header->setAttribute('class', 'fluent-card-header');
        $titleSpan = $dom->createElement('span', 'Informations');
        $titleSpan->setAttribute('class', 'fluent-card-title');
        $header->appendChild($titleSpan);
        $aside->appendChild($header);

        // Body
        $bodyDiv = $dom->createElement('div');
        $bodyDiv->setAttribute('class', 'fluent-card-body');

        // Sous-titre (ex: "Prochaines grilles :")
        if ($titre) {
            $subtitle = $dom->createElement('h2', htmlspecialchars($titre));
            $subtitle->setAttribute('class', 'fluent-card-subtitle');
            $bodyDiv->appendChild($subtitle);
        }

        // Liste
        if ($liste) {
            // On clone la liste pour la réutiliser
            $newUl = $liste->cloneNode(true);
            $newUl->setAttribute('class', 'fluent-list');
            // On adapte les icônes FontAwesome en préservant les classes de couleur
						foreach ($newUl->getElementsByTagName('i') as $i) {
								$currentClasses = $i->getAttribute('class');
								$colorClass = '';

								// Extraire la classe de couleur (vert, bleu, rouge)
								if (preg_match('/(vert|bleu|rouge)/', $currentClasses, $matches)) {
										$colorClass = $matches[1];
								}

								// Déterminer l'icône appropriée selon la classe actuelle
								if (strpos($currentClasses, 'fa-circle-check') !== false) {
										$newClass = 'fa-solid fa-circle-check fluent-success';
								} elseif (strpos($currentClasses, 'fa-circle-exclamation') !== false) {
										$newClass = 'fa-solid fa-circle-exclamation fluent-info';
								} elseif (strpos($currentClasses, 'fa-triangle-exclamation') !== false) {
										$newClass = 'fa-solid fa-triangle-exclamation fluent-warning';
								} else {
										// Icône par défaut si aucune correspondance
										$newClass = '';
								}

								// Ajouter la classe de couleur si elle existe
								if ($colorClass) {
										$newClass .= ' ' . $colorClass;
								}

								$i->setAttribute('class', $newClass);
						}
            $bodyDiv->appendChild($newUl);
        }

        $aside->appendChild($bodyDiv);

        // Remplacement dans le DOM
        $infoProno->parentNode->replaceChild($aside, $infoProno);
    }
	}
	
	function _setTheader(&$xpath, &$dom, $selecteur) {
		$tables = $xpath->query("//table[contains(@class, '" . $selecteur . "')]");
		foreach ($tables as $table) {
        // Ajout de la classe table-data
        $table->setAttribute('class', $selecteur .' table-data');

        // 2. THEAD : remplacer les <td> par <th>
        $thead = $xpath->query(".//thead", $table)->item(0);
        if ($thead) {
            foreach ($xpath->query(".//td", $thead) as $td) {
                $th = $dom->createElement('th');
                // Copier le contenu
                foreach (iterator_to_array($td->childNodes) as $child) {
                    $th->appendChild($child->cloneNode(true));
                }
                // Copier les classes
                if ($td->hasAttributes()) {
                    foreach ($td->attributes as $attr) {
                        $th->setAttribute($attr->nodeName, $attr->nodeValue);
                    }
                }
                $td->parentNode->replaceChild($th, $td);
            }
        }
		}
	}
	
	function _processFluentBench(&$xpath, &$dom) {
		
		//remplacer les td par th dans thead
		$this->_setTheader($xpath, $dom, 'bench-resultat');
		$this->_setTheader($xpath, $dom, 'bench-index');
		
    $tables = $xpath->query("//table[contains(@class, 'bench-resultat')]");
    foreach ($tables as $table) {
       
        //TFOOT : styliser la ligne de moyenne
        $tfoot = $xpath->query(".//tfoot", $table)->item(0);
        if ($tfoot) {
            foreach ($xpath->query(".//tr", $tfoot) as $tr) {
                $tds = $xpath->query(".//td", $tr);
                if ($tds->item(0)->hasAttribute('colspan') && ($tds->item(0)->getAttribute('colspan') == 6)) {
										$tds->item(0)->setAttribute('class', 'forTable padding0');
                    $moyenne = $dom->createElement('div');
                    $moyenne->setAttribute('class', 'moyenne');
                    
                    // Récupérer le texte du td, supprimer les br et mettre le contenu dans le div
                    $td = $tds->item(0);
                    $textContent = '';
                    
                    // Parcourir tous les nœuds enfants
                    foreach ($td->childNodes as $child) {
                        if ($child->nodeType === XML_TEXT_NODE) {
                            $textContent .= $child->nodeValue;
                        } elseif ($child->nodeName === 'br') {
                            $textContent .= ' '; // Remplacer <br> par un espace
                        } else {
                            // Pour les autres éléments, récupérer leur contenu texte
                            $textContent .= $child->textContent;
                        }
                    }
                    
                    // Nettoyer le texte (supprimer les espaces multiples)
                    $textContent = preg_replace('/\s+/', ' ', trim($textContent));
                    
                    // Ajouter le texte nettoyé au div moyenne
                    $moyenne->appendChild($dom->createTextNode($textContent));
                    
                    // Supprimer l'ancien contenu du td
                    while ($td->firstChild) {
                        $td->removeChild($td->firstChild);
                    }
                    
                    // Remplacer le div à la place de l'ancien contenu du td
                    $td->appendChild($moyenne);
                }
            }
        }
    }
		
		// Toilettage du tableau d'index en virant les colonnes qui ne servent à rien.
		$tables = $xpath->query("//table[contains(@class, 'bench-index')]");
		foreach ($tables as $table) {
				// 1. Nettoyer l'en-tête (thead)
				$thead = $xpath->query(".//thead", $table)->item(0);
				if ($thead) {
						foreach ($xpath->query(".//tr", $thead) as $tr) {
								$cells = [];
								foreach ($xpath->query(".//th|.//td", $tr) as $cell) {
										$cells[] = $cell;
								}
								// On garde la 2e (index 1) et la 4e (index 3) colonne
								foreach ($cells as $i => $cell) {
										if (!in_array($i, [1, 3])) {
												$cell->parentNode->removeChild($cell);
										}
								}
						}
				}

				// 2. Nettoyer le corps (tbody)
				$tbody = $xpath->query(".//tbody", $table)->item(0);
				if ($tbody) {
						foreach ($xpath->query(".//tr", $tbody) as $tr) {
								$tds = [];
								foreach ($xpath->query(".//td", $tr) as $td) {
										$tds[] = $td;
								}
								// On garde la 1ère (index 0) et la 2e (index 1) colonne utile
								// (la 1ère colonne a souvent un colspan, on le retire)
								if (isset($tds[0]) && $tds[0]->hasAttribute('colspan')) {
										$tds[0]->removeAttribute('colspan');
								}
								// Supprimer toutes les colonnes sauf index 0 et 1
								foreach ($tds as $i => $td) {
										if (!in_array($i, [0, 1])) {
												$td->parentNode->removeChild($td);
										}
								}
						}
				}
		}

}


	
	function processBody($body) {
		$body = parent::processBody($body);

		// Pour DOMDocument, il faut un document complet
		$body = '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $body . '</body></html>';

		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		$xpath = new DOMXPath($dom);

		$this->_processFluentIndex($xpath, $dom);
		$this->_processFluentPronofoot($xpath, $dom);
		$this->_processFluentBench($xpath, $dom);
		
		// Traitement des bulles d'informations
		foreach ($dom->getElementsByTagName('*') as $el) {
				if ($el->hasAttribute('class')) {
						$classes = explode(' ', $el->getAttribute('class'));
						$newClasses = [];
						foreach ($classes as $class) {
								switch ($class) {
										case 'information':
												$newClasses[] = 'card';
												$newClasses[] = 'card--info';
												break;
										case 'warning':
												$newClasses[] = 'card';
												$newClasses[] = 'card--warning';
												break;
										case 'prix':
												$newClasses[] = 'card';
												$newClasses[] = 'card--warning'; // ou card--prix si tu crées la variante
												break;
										default:
												$newClasses[] = $class;
								}
						}
						$el->setAttribute('class', implode(' ', array_unique($newClasses)));
				}
		}
		
		// Ajoute la classe "card"
		foreach ($dom->getElementsByTagName('td') as $div) {
				if ($div->hasAttribute('class')) {
						$classes = explode(' ', $div->getAttribute('class'));
						if (in_array('archive', $classes) && !in_array('card', $classes)) {
								$classes[] = 'card';
								$div->setAttribute('class', implode(' ', array_unique($classes)));
						}
				}
		}
		
		// On retourne le HTML du body sans les balises html/body
		$newBody = '';
		foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
				$newBody .= $dom->saveHTML($node);
		}
		return $newBody;
	}

}
