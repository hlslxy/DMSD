git -f filter-branch --commit-filter '
        if [ "$GIT_AUTHOR_EMAIL" = "xyliu@theobook160.rz-berlin.mpg.de" ];
        then
                GIT_AUTHOR_NAME="hlslxy";
                GIT_AUTHOR_EMAIL="hlslxy@gmail.com";
                git commit-tree "$@";
        else
                git commit-tree "$@";
        fi' HEAD
