# Vagrant Ubuntu 16.04 LEMP

Macchina Vagrant per il corso introduttivo a Magento 2 per sviluppatori. Vedi: http://magma.consulting/corsi

## Introduzione

Questa macchina Vagrant Box comprende l'installazione di un LEMP (Ubuntu 16.04, Nginx, MySQL and PHP)  ed è adatta per essere usata come ambiente di sviluppo per progetti PHP e Magento 2.

- IP: **192.168.100.16**
- Default SSH Port: **22**
- Linux Login: **vagrant** / **vagrant**
- MySQL Login: **root** / **root**

## Prerequisiti

I seguenti programmi devono essere installati sulla propria macchina:

+ [VirtualBox](https://www.virtualbox.org/)
+ [Vagrant](https://www.vagrantup.com/)

## Istruzioni

Prima installazione:
- Modificare il proprio file `hosts` aggiungendo la riga `192.168.100.16 vu16lemp magento21.dev magento22.dev`
- Decomprimere il file zip e mettere i file in una nuova cartella chiamandola per esempio `vu16lemp`
- Aprire una sessione del terminale nella cartella del progetto e lanciare il comando:  `vagrant up`
- La prima volta sarà necessario attendere lo scaricamento della macchina base e il completamento della procedura di installazione
- Fine. Accedere alla shell della macchina ospite con `vagrant ssh`

Successive sessioni di lavoro:
- Accedere in shell nella cartella dove è presente Vagrantfile e lanciare `vagrant up`

## Utenti windows

Gli utenti con macchine windows potrebbero necessitare di operazioni leggermente diverse per:

* SSH: potrebbe essere necessario accedere alla macchina con un client ssh (es. putty);
* Sincronizzazione delle cartelle: potrebbe essere neecessario una maggiore configurazione per gestire la sincronizzazione dei link simbolici;
