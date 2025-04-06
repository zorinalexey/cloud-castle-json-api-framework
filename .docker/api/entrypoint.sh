#!/bin/bash
# Запускаем Supervisor для управления процессами
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisor.conf