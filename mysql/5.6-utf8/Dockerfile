FROM mysql:5.6

RUN { \
    echo '[mysqld]'; \
    echo 'character-set-server = utf8'; \
} | tee > /etc/mysql/conf.d/default-character-set.cnf
