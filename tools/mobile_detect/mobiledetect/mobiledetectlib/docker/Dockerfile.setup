FROM composer:latest AS build
WORKDIR /app
COPY . .
COPY ./docker/build.sh .
RUN pwd
# Make the script executable
SHELL ["/bin/bash", "-c"]
RUN chmod +x build.sh
RUN ./build.sh
