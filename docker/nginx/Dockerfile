FROM nginx:1.18

COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./sites-available /etc/nginx/sites-available/

RUN rm -f /etc/nginx/conf.d/default.conf && \
	mkdir /etc/nginx/sites-enabled && \
	ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default.conf