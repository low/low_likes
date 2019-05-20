# Low Likes for ExpressionEngine

Like and unlike entries and members.

## Tags

### `{exp:low_likes:total}``

Returns the total amount of likes (entries and members) by the logged in member.

### `{exp:low_likes:total_entries}`

Returns the total amount of entries liked by the logged in member.

### `{exp:low_likes:total_members}`

Returns the total amount of members liked by the logged in member.

### `{exp:low_likes:form}`

Use this tag pair to generate a form to toggle a like for the logged in member. Add either an input field with `name="entry_id"` or `name="member_id"` to target the right content type.

#### Parameters

- `form:ATTRIBUTE=`: any html attribute for the form-element, eg. `form:id="like-me"`

#### Example

    {exp:low_likes:form form:id="like-me" form:data-foo="bar"}
        <button name="entry_id" value="{entry_id}">Toggle</button>
    {/exp:low_likes:form}

### `{exp:low_likes:entry}`

Use this tag pair to determine if a given entry is liked or not by the logged in member.

#### Parameters

- `id=`: the target entry ID.

#### Example

    {exp:low_likes:entry id="{entry_id}"}
        <button type="submit" name="entry_id" value="{entry_id}" form="like-me">
            {if is_liked}Unlike{if:else}Like{/if}
        </button>
    {/exp:low_likes:entry}

### `{exp:low_likes:member}`

Use this tag pair to determine if a given member is liked or not by the logged in member.

#### Parameters

- `id=`: the target entry ID.

#### Example

    {exp:low_likes:member id="{member_id}"}
        <button type="submit" name="member_id" value="{member_id}" form="like-me">
            {if is_liked}Unlike{if:else}Like{/if}
        </button>
    {/exp:low_likes:member}

### `{exp:low_likes:entry_ids}`

Use this tag to output a pipe-separated list of entry IDs liked by the logged in member.

### `{exp:low_likes:member_ids}`

Use this tag to output a pipe-separated list of member IDs liked by the logged in member.

### `{exp:low_likes:entries}`

Use this tag pair to output all entries that the current member liked. This tag calls the native Channel Entries tag, so you can use any parameter that tag has available.

#### Example

    <h2>My fave products</h2>
    {exp:low_likes:entries channel="products"}
        <h3>{title}</h3>
    {/exp:low_likes:entries}
