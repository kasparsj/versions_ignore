# Versions Ignore

This app let's you exclude files or folders from versioning. 
It works similar to **gitignore** , using a `.versions_ignore` file you can specify which files or folders should not be versioned.
Already created file versions are not affected.
Each line specifies a pattern.   

## Examples

```
#.versions_ignore

# match filename, also matches file in subdirectories
image.jpg

# negate pattern
!test.php

# wildcard extension
test.*

# wildcard filename
*.jpg

# + matches at least 1 characted (does not match ".jpg", but matches "a.jpg") 
+.jpg

# match everyhting inside the directory
*

# match all directories with a .gitignore file 
**.gitignore
```

## Installing

Download and extract to **nextcloud/apps/**, and enable the app in Nextcloud settings.
