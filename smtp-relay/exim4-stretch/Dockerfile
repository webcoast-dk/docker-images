FROM debian:stretch

# Install exim4 light
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        exim4-daemon-light

# Prepare config update script to use smarthost configuration type,
# the `smarthost` environment variable for the smarthost and enable
# the split configuration to use
RUN { \
    echo "dc_local_interfaces=''"; \
    echo "dc_eximconfig_configtype='smarthost'"; \
    echo "dc_relay_nets='\${env{relay_nets}{\$value}{}}'"; \
    echo "dc_smarthost='\${env{smarthost}{\$value}{}}'"; \
    echo "dc_use_split_config='true'"; \
} | tee >> /etc/exim4/update-exim4.conf.conf

# Add `smarthost` to the kept environment variables
RUN echo "MAIN_KEEP_ENVIRONMENT=smarthost:relay_nets" > /etc/exim4/conf.d/main/01_keep_environment
# Update the configuration file
RUN update-exim4.conf

EXPOSE 25

CMD ["exim4", "-q30m", "-bdf"]