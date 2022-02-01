#!/bin/bash
set -x
awslocal s3 mb s3://default

# Create dummy file
echo "testFile s3" >> test.txt
# Create a file if you don't already have it
awslocal s3 cp test.txt s3://default
