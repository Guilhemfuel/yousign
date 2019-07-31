# Symfony YouSign Project

## Installation
    
1. Clone this repository
    ```
    git clone https://github.com/Guilhemfuel/yousign.git
    ```
2. Install Symfony Vendor
    ```
    composer install
    ```
3. Install and launch the containers
    ```
    docker-compose build
    docker-compose up -d
    ```

4. Build the BDD
    ```
    docker exec -it yousign_php php /var/www/yousign/bin/console doctrine:schema:update --force
    ```
    
5. Go to your browser !
    ```
    http://www.yousign.local
    ```

### Some helpful links

    App : http://www.yousign.local
    phpmyadmin : http://www.yousign.local:8080 (yousign yousign)
    maildev : http://www.yousign.local:1080
    rabbitmq : http://www.yousign.local:60005 (admin admin)
    documentation : http://www.yousign.local/api/doc

### Some helpful command
    Build dev : docker exec -it yousign_node_1 yarn encore dev --watch
    Build prod : docker exec -it yousign_node_1 yarn encore production
    docker exec -it yousign_php php /var/www/yousign/bin/console cache:clear
    
    Kill container : 
    docker stop $(docker ps -aq)
    docker rm $(docker ps -aq)