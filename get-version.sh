#!/bin/sh
#
# Print a human-readable version for this Composer package.

set -e

# Try to extract the static package version from composer.json:
static_version=$(jq -r .version composer.json)
case "X${static_version?}X" in
    XnullX | XX)
        # Unset or empty; ignore.
        ;;

    *)
        # Non-empty, use it.
        echo "${static_version?}"
        exit
esac

# There's no static version, but maybe the current commit is tagged?
#
# If so, use the first tag that looks like a version, after version-sorting the tags
# in descending order.
current_tag=$(git tag --no-column --points-at HEAD | sed 's/^[vV]//' | grep '^[0-9]' \
    | sort -Vr | head -n 1)
if [ -n "${current_tag?}" ]; then
    echo "${current_tag?}"
    exit
fi

# Otherwise, if we are on a branch, use its name.
branch=$(git symbolic-ref -q --short HEAD || true)
if [ -n "${branch?}" ]; then
    echo "dev-${branch?}"
    exit
fi

# Otherwise, we couldn't get a version.
exit 42
