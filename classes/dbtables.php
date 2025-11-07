<?php
#
# database tables
#
namespace VNVE;

/**
 * grenzenloos tabel
 */
class Dbtables
{
    /**
	 * function to get the table information
	 */
	public function tables()
	{
   		$reflect = new \ReflectionClass(get_class($this));
    	return $reflect->getConstants();
	}
	/**
	* documenten
	*/
	const docman = ["name"=>"docman", "columns"=>"
		`id`                    int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `crdate`                datetime NOT NULL,		    #creationdate of record
    	`docid`                 varchar(255),				#reference id of document
		`title`                 varchar(255),				#title
		`author`                varchar(255),				#author
		`publisher`             varchar(255),			    #publisher
		`pubdate`               date NOT NULL,			    #date published
        `expirationdate`        date NOT NULL,			    #expiration date
        `status`                varchar (16),			    #status: open/restricted (empty=open)
		`description`           varchar(1024),		        #author
		`rubric`                varchar(255),				#rubric
        `viewlevel`             varchar(255),			    #viewlevel
        `doctype`               varchar(16),			    #type of document (pdf/image)
        `document`              varchar(255),			    #link to document
        `internallink`          varchar(255),			    #link to page in current website
        `externallink`          varchar(255),			    #external link
		PRIMARY KEY (`id`)"]; 
    /**
     * titels van opgeslagen artikelen
     */
	const titels = ["name"=>"grenzenloos", "columns"=>"
		`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `crdate` datetime NOT NULL,					#creationdate of record
        `nummer` int(5) NOT NULL,		#nummer
        `oudnummer` int(5) NOT NULL,				#oud nummer
        `seizoen` varchar(255) NOT NULL,
        `titel` varchar(512) NOT NULL,
        `auteur` varchar(255) NOT NULL,						#auteur
        `bladzijden` varchar(255) NOT NULL,			#bladzijden
        `artikel` varchar(255),
		PRIMARY KEY (`id`)"]; 
        /**
         * lidmaatschapformulier
         */
    const leden = ["name"=>"leden", "columns"=>"
		`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `crdate` datetime NOT NULL,					#creationdate of record
        `voorletters` varchar(16) NOT NULL,
		`roepnaam` varchar(64),
		`achternaam` varchar(64) NOT NULL,
		`geboortedatum` varchar(16),
		`straat` varchar(64),
		`huisnummer` varchar(16),
		`postcode` varchar(16),
		`woonplaats` varchar(64),
        `telefoon` varchar(16),
		`mobiel` varchar(16),
        `email` varchar(64),
        `bank` vrachar(64),
        `beroep` varchar(64),
        `interesse` varchar(256),
        `vereniging` varchar(16),
        `vereniging_naam` varchar(128),
        `vereniging_plaats` varchar(128),
        `hobby` varchar(16),
        `hobby_naam` varchar(128),
        `bestuur` varchar(16),
        `bestuur_vereniging` varchar(128),
        `bestuur_functie` varchar(128),
        `bestuur_van` varchar(16),
        `bestuur_tot` varchar(16),
        `interesse` varchar(16),
        `interesse_gebied` varchar(128),
		PRIMARY KEY (`id`)"]; 
}
?>