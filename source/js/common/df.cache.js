if (typeof  df === 'undefined' || !df.init_common) {
    alert('need include df.js for df.cache.js ');
}

df.init_cache = true;
df.cache = {};

df.cache.store = {

    __get: function (key) {
        var result = null;
        if ($.totalStorage) {
            result = $.totalStorage(key);
        } else {
            result = df.get(key);
        }
        return result;
    },
    __set: function (key, data) {
        if ($.totalStorage) {
            $.totalStorage(key, data);
        } else {
            df.set(key, data);
        }
    },
    get: function (key) {
        var result = this.__get(key);

        if (typeof result == "undefined" || result == null) {
            return null;
        }

        var data = result.data;
        var expire = result.expire;
        var now = Date.now();

        if (result && now > expire) {
            this.delete(key);
            data = null;
        }

        return data;
    },
    set: function (key, data, ttl) {
        var expire = Date.now() + ttl * 1000;
        this.__set(key, {data: data, expire: expire});

        var store_keys = this.__get('store:keys') || [];
        var isset = false;
        for (var index in store_keys) {
            if (store_keys[index] == key) {
                isset = true;
                break;
            }
        }
        if (!isset)
            store_keys.push(key);

        this.__set('store:keys', store_keys);
    },
    keys: function () {
        return this.__get('store:keys');
    },
    delete: function (key) {
        this.__set(key, null);
        var store_keys = this.__get('store:keys');
        if (store_keys.length != 0)
            for (var index in store_keys) {
                if (store_keys[index] == key) {
                    delete store_keys[index];
                    break;
                }
            }
        this.__set('store:keys', store_keys);
    }

}


df.cache.get = function (key) {
    return df.cache.store.get(key);
}

df.cache.set = function (key, data, ttl) {
    return df.cache.store.set(key, data, ttl);
}

df.cache.delete = function (key) {
    df.cache.store.delete(key);
}

df.cache.clear = function () {
    var keys = df.cache.store.keys();
    for (var index in keys) {
        df.cache.store.delete(keys[index]);
    }
}

df.cache.debug = function () {
    var keys = df.cache.store.keys();
    for (var index in keys) {
        var data = df.cache.store.get(keys[index]);
        if (data != null)
            df.log(data, keys[index]);
    }
}