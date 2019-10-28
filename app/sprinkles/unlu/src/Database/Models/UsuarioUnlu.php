<?php
namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnluAux;
use UserFrosting\Sprinkle\Unlu\Database\Scopes\UsuarioUnluAuxScope;

/**
 * Trait used to attach handlers to events for our model. In this case, we use the saved "event"
 * to tell Laravel to save the related "UsuarioUnluAux" model any time the "UsuarioUnlu" is saved. It will
 * also call "createAuxIfNotExists" which... well, does exactly what the name says it does.
 */
trait LinkUsuarioUnluAux {
    /**
     * The "booting" method of the trait.
     *
     * @return void
     */
    protected static function bootLinkUsuarioUnluAux()
    {
        /**
         * Create a new UsuarioUnluAux if necessary, and save the associated usuario_unlu data every time.
         */
        static::saved(function ($usuario_unlu) {
            $usuario_unlu->createAuxIfNotExists();

            if ($usuario_unlu->auxType) {
                // Set the aux PK, if it hasn't been set yet
                if (!$usuario_unlu->aux->id) {
                    $usuario_unlu->aux->id = $usuario_unlu->id;
                }

                $usuario_unlu->aux->save();
            }
        });
    }
}

class UsuarioUnlu extends User {
    use LinkUsuarioUnluAux;

    protected $fillable = [
        'user_name',
        'first_name',
        'last_name',
        'email',
        'locale',
        'theme',
        'group_id',
        'flag_verified',
        'flag_enabled',
        'last_activity_id',
        'password',
        'deleted_at',

        'telefono',
        'institucion',
        'dependencia',
        'rol',
        'activo'
    ];

    protected $auxType = 'UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnluAux';

    /**
     * Required to be able to access the `aux` relationship in Twig without needing to do eager loading.
     * @see http://stackoverflow.com/questions/29514081/cannot-access-eloquent-attributes-on-twig/35908957#35908957
     */
    public function __isset($name) {
        if (in_array($name, [
            'aux'
        ])) {
            return true;
        } else {
            return parent::__isset($name);
        }
    }

    /**
     * Globally joins the `usuario_unlu` table to access additional properties.
     */
    protected static function boot() {
        parent::boot();

        static::addGlobalScope(new UsuarioUnluAuxScope);
    }

    /**
     * Custom mutator for UsuarioUnlu property
     */
    public function setTelefonoAttribute($value)
    {
        $this->createAuxIfNotExists();

        $this->aux->telefono = $value;
    }

    /**
     * Custom mutator for UsuarioUnlu property
     */
    public function setInstitucionAttribute($value)
    {
        $this->createAuxIfNotExists();

        $this->aux->institucion = $value;
    }

    /**
     * Custom mutator for UsuarioUnlu property
     */
    public function setDependenciaAttribute($value)
    {
        $this->createAuxIfNotExists();

        $this->aux->dependencia = $value;
    }

    /**
     * Custom mutator for UsuarioUnlu property
     */
    public function setRolAttribute($value)
    {
        $this->createAuxIfNotExists();

        $this->aux->rol = $value;
    }

    /**
     * Custom mutator for UsuarioUnlu property
     */
    public function setActivoAttribute($value)
    {
        $this->createAuxIfNotExists();

        $this->aux->activo = $value;
    }

    /**
     * Relationship for interacting with aux model (`usuario_unlu` table).
     */
    public function aux()
    {
        return $this->hasOne($this->auxType, 'id');
    }

    /**
     * If this instance doesn't already have a related aux model (either in the db on in the current object), then create one
     */
    protected function createAuxIfNotExists()
    {
        if ($this->auxType && !count($this->aux)) {
            // Create aux model and set primary key to be the same as the main user's
            $aux = new $this->auxType;

            // Needed to immediately hydrate the relation.  It will actually get saved in the bootLinkUsuarioUnluAux method.
            $this->setRelation('aux', $aux);
        }
    }
}