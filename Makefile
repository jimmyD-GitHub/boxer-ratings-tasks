build:
	docker-compose build

start:
	docker-compose up -d

stop:
	docker-compose down

exec:
	docker exec -it tasks bash

mysql:
	docker exec -it mysql bash

rsync:
ifneq ($(wildcard vendor ),)
	$(info Vendor exists, including it)
	$(MAKE) rsync-vendor
else
	$(MAKE) rsync-no-vendor
endif

# RSYNC with vendor always
rsync-vendor:
	$(call count,rsync-vendor)
	env RSYNC_VENDOR='--include=vendor --filter="+ vendor"' $(MAKE) _rsync

# RSYNC without vendor always
rsync-no-vendor:
	$(call count,rsync-no-vendor)
	$(MAKE) _rsync

# Internal rsync function, not to be called directly
_rsync:
	$(call mark,rsync)
	@printf "tasks" | xargs -n1 -P1 -ICONTAINER rsync \
		-e "docker exec -i" --blocking-io -avz --delete \
		--no-perms --no-owner --no-group \
		$(RSYNC_VENDOR) \
		--exclude-from=".dockerignore" \
		--exclude-from=".gitignore" \
		--checksum \
		--no-times \
		--itemize-changes \
		. CONTAINER:/var/www/boxer-ratings-tasks/
	$(call send,rsync)
