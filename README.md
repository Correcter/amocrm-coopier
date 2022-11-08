#### AmoCrmCopier.

**A system for transferring and synchronizing all possible changes from one AMOCRM account to another**

*Installation and Deployment*

- For **DEV** - environment, make a call in the root directory of the project: 

``` composer install --no-dev```

For combat:

``` composer install ```

- During installation, the system will ask you for information to copy: for the base account and for the combat account.
- The installer must pull up all the necessary `vendor` packages.
- Edit the file with the parameters ``(.app/config/parameters.yml.dist)`` if you want to change the data entered during the installation of the project.

#### Sync commands

- To synchronize transactions with all related entities, run from the command line:

```bin/console basic-to-target:deal```

If successful, a response of the following type will be output:

``` 
Added transactions: n
Added tasks: n
Added companies: n
Added contacts: n
``
- To update the transaction statuses from the target account to the base account, you should perform:

```bin/console target-to-basic:updateStatus```

- In case of success:

`` Transaction statuses:.... successfully synchronized! ```

- If for some reason the command works with errors, the corresponding exception will be thrown.
