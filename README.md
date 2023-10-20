# Lamborghini - CMS

This repository is the CMS software for Lamborghini.

## Main ideas and goals behind the project

It is the software used by Hibo to publish content for Lamborghini's SmartTV app and is integrated with Meride, Firebase, Akamai Image Manager.

## Technologies used

List of the technologies used in the project.

- PHP 8
- Laravel 9
- Vite
- AlpineJS
- Tailwind CSS

## Requirements

List of the requirements of the software.

- PHP >= 8.0
- Node.js >= 16

## Installation 

List of common steps for the installation of the software.

**Common commands:**

- `composer install`
- `npm install`
- `php artisan migrate`
- `php artisan key:generate`
- `php artisan storage:link`
- `npm run build`

You will need to download the json file from the service account of the Firebase project and place it in the root directory of the project.

**Environment variables:**

- `FIREBASE_PROJECT_ID`: Firebase project ID
- `FIREBASE_CREDENTIALS`: the name of the json file downloaded from the service account of the Firebase project, placed in the root directory of the project
- `FIREBASE_DATABASE_URL`: the base URL of the Firebase real time database

- `FIRESTORE_EMULATOR_HOST`: optional. When developing, the HOST of the Firebase emulator
- `FIREBASE_AUTH_EMULATOR_HOST`: optional. When developing, the HOST of the Auth Firebase emulator
- `FIREBASE_DATABASE_EMULATOR_HOST`: : optional. When developing, the HOST of the Database Firebase emulator

- `MERIDE_CLIENT_ID`: Meride client ID
- `MERIDE_AUTH_CODE`: Meride auth code
- `MERIDE_CMS_URL`: Meride CMS base URL
- `MERIDE_WEBHOOK_SECRET_KEY`: Meride Webhook secret
- `MERIDE_ENCODER_PUBLIC`: the name of the slot in Meride CMS for the encoding of public videos
- `MERIDE_ENCODER_PRIVATE`: the name of the slot in Meride CMS for the encoding of protected videos
- `MERIDE_STORAGE_UPLOAD_ENDPOINT`: TUS service upload base URL
- 
- `AKAMAI_VIDEO_TOKEN_AUTH_KEY`: the Akamai key used to generate tokens for protected videos

- `MEDIA_FTP_HOST`: the FTP location of the media assets, such as posts images
- `MEDIA_FTP_USERNAME`: FTP username credentials
- `MEDIA_FTP_PASSWORD`: FTP password credentials
- `MEDIA_FTP_PUBLIC_BASE_URL`: the base URL of the public media storage

- `PUBLIC_SITE_BASE_URL`: the base URL of the public front-end site

- `PALIMPSEST_XML_RESOURSE_DRIVER`: TV palimpsest feed xml - driver (ex. ftp)
- `PALIMPSEST_XML_RESOURSE_HOST`: TV palimpsest feed xml - host
- `PALIMPSEST_XML_RESOURSE_USER`: TV palimpsest feed xml - user
- `PALIMPSEST_XML_RESOURSE_PASS`: TV palimpsest feed xml - pass
- `PALIMPSEST_XML_RESOURSE_PORT`: TV palimpsest feed xml - port
- `PALIMPSEST_XML_RESOURSE_BASE_PATH`: TV palimpsest feed xml - base path
- `PALIMPSEST_XML_TV_NAME`: TV palimpsest feed xml - file name


### Development

List of the installation steps for a development environment.

**Development commands:**

- `php artisan serve`
- `npm run dev`

### Development with docker


- (first time only) `docker exec tv2000_webserver bash -c 'composer install; npm i; npm run build; php artisan migrate; php artisan cache:clear; php artisan config:clear'`
- `cd docker/web`
- `docker build -t mosaicodev/lamborghinicms:0.0.2 .`
- `cd ..`
- `cp .env-example .env`

Edit .env file

- `docker-compose up`

### Production

List of the installation steps for a production environment.

**Production commands:**

- `npm run build`

## Usage

There are a few php artisan commands available.

**Main commands:**

- `php artisan` to view the available commands

In order to create first root user to login on the CMS use

- `php artisan user:create_root {name} {email} {password}`

## Deployment

All the deployments should be automated.

## Start the Firebase emulator

- Copy the file firebase.json.example in firebase.json
- from the root directory run `firebase emulators:start -c ./firebase.json`
- setup the .env file or export the environment variable for the local Firebase Firestore: `export FIRESTORE_EMULATOR_HOST="localhost:8080"` (or host.docker.internal instead of localhost in case of Docker)
- setup the .env file or export the environment variable for the local Firebase Firestore: `export FIREBASE_AUTH_EMULATOR_HOST="localhost:9099"` (or host.docker.internal instead of localhost in case of Docker)
- setup the .env file or export the environment variable for the local Firebase Firestore: `export FIREBASE_DATABASE_EMULATOR_HOST="localhost:9000"` (or host.docker.internal instead of localhost in case of Docker)

In order to persist the Firebase data launch the start command like that: `firebase emulators:start -c ./firebase.json --export-on-exit=/PATH_TO_DIR/firestore_export --import /PATH_TO_DIR/firestore_export`


