FROM koolob/swoole-docker
EXPOSE 9797
ADD examples /tmp/examples
ADD src /tmp/src
ADD vendor /tmp/vendor
CMD php /tmp/examples/rpc_server.php