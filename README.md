
- Run docker-compose:

```bash
docker-compose up -d
```

run in console:

```bash
php index.php
```

You will see the menu in list of actions and you can choose one of them.
by number of action.



- [x] Migrate to a full PSR-6 Laravel container
- [ ] setup illuminate/config instead of Config class.
- [ ] upgrade menus
- [ ] Setup models:
    `Vault`:
         - name:string 
         - path:string 
         - created_at:datetime 
         - updated_at:datetime
- [x] Use packages


# ./database/vaults.json
```json
[
    {name: "Password for work", path : "/passwords/passwords_for_work.json", created_at: ..., updated_at: ...},
    {name: "Password for home", path : "/passwords/passwords_for_home.json", ...},
]
```
