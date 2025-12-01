pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                // Stage Checkout tetap sama. Jika repo private, pastikan kredensial disertakan.
                git branch: 'main', url: 'https://github.com/agistazllk/pipeline.git'
            }
        }
        
        stage('Composer Install') {
            steps {
                // Menggunakan powershell untuk menjalankan Composer
                powershell 'composer install --no-interaction --prefer-dist'
            }
        }
        
        stage('Run PHPUnit Tests') {
            steps {
                // Menggunakan powershell untuk menjalankan PHPUnit dari vendor/bin
                // Pastikan PHP dan Composer ada di PATH atau sediakan path lengkap jika perlu.
                powershell './vendor/bin/phpunit tests --testdox --colors=never --no-coverage'
            }
        }
        
        stage('hello') {
            steps {
                echo 'Hallo Agista Zulkarnain'
            }
        }
    }
}