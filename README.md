# Coder

How to run:
```bash
vendor/bin/rector process --autoload-file path/to/project/vendor/autoload.php\\
   --only "\CrmPlease\Coder\Rector\AddToReturnArrayByOrderRector"\\
   path/to/project/Path/To/Class.php
```

Config is in rector.yaml, you can provide method name to setMethod, value to setValue, constant to setConstant.
