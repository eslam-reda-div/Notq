IMAGE_NAME ?= notq-laravel
REGISTRY ?= ghcr.io/notq

TAG ?= latest
IMAGE := $(IMAGE_NAME):$(TAG)
REMOTE_IMAGE := $(REGISTRY)/$(IMAGE_NAME):$(TAG)

.PHONY: build tag push deploy

build:
	docker build -t $(IMAGE) --build-arg ENV_TYPE=prod .

tag:
	docker tag $(IMAGE) $(REMOTE_IMAGE)

push:
	docker push $(REMOTE_IMAGE)

deploy: build tag push
