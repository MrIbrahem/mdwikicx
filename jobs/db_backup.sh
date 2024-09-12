#!/bin/bash
# tfj run db --image mariadb --command "$HOME/jobs/db_backup.sh"
export PATH=/data/project/mdwiki/local/bin:/usr/local/bin:/usr/bin:/bin

db_name="s55992__wiki_cx"

backup_file="databasebackup/wiki-$(date -I).sql"
echo "backup_file: $backup_file"

#umask o-r # dump should not be public (unless the database is)

echo "Starting backup..."

mysqldump --defaults-file=~/replica.my.cnf --host=tools.db.svc.wikimedia.cloud "$db_name" > ~/"$backup_file"

#umask 0022 # restore default umask

if [ $? -eq 0 ]; then
    echo "Backup completed successfully"
else
    echo "Backup failed."
fi
