FROM task_manager-php-base

ARG COMMON_NAME
ARG HTTP_SERVER_PORT

ARG QUEUE_NAME=${COMMON_NAME}
ARG MESSENGER_WORKER_PATH=/etc/supervisor/conf.d/messenger-worker.conf

ENV DATABASE_DB ${COMMON_NAME}
ENV APP_CACHE_DIR ${COMMON_NAME}
ENV APP_LOG_DIR ${COMMON_NAME}
ENV HTTP_SERVER_PORT ${HTTP_SERVER_PORT}

COPY ./docker/configs/php /usr/local/etc/php/conf.d

RUN mkdir -p /opt/home && chmod 777 /opt/home
ENV HOME /opt/home

RUN touch $MESSENGER_WORKER_PATH && \
    echo "[program:messenger-consume]" >> $MESSENGER_WORKER_PATH && \
    echo "command=php /var/www/symfony/bin/console messenger:consume async --queues=$QUEUE_NAME --time-limit=3600" >> $MESSENGER_WORKER_PATH && \
    echo "user=root" >> $MESSENGER_WORKER_PATH && \
    echo "numprocs=2" >> $MESSENGER_WORKER_PATH && \
    echo "autostart=true" >> $MESSENGER_WORKER_PATH && \
    echo "autorestart=true" >> $MESSENGER_WORKER_PATH && \
    echo "process_name=%(program_name)s_%(process_num)02d" >> $MESSENGER_WORKER_PATH && \
    echo "environment=DATABASE_DB=${DATABASE_DB},APP_CACHE_DIR=${APP_CACHE_DIR},APP_LOG_DIR=${APP_LOG_DIR}" >> $MESSENGER_WORKER_PATH

CMD ["sh", "-c", "service supervisor stop && service supervisor start && symfony serve --dir=symfony/public --port=${HTTP_SERVER_PORT}"]