<?php
/**
 * ISO Codes
 *
 * Copyright © 2016 Juan Pedro Gonzalez Gutierrez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */
namespace ISOCodes\I18n;

/**
 * Translator.
 */
class Translator implements TranslatorInterface
{
    /**
     * Default locale.
     *
     * @var string
     */
    protected $locale;
    
    /**
     * Messages loaded by the translator.
     *
     * @var array
     */
    protected $messages = [];
    
    /**
     * Get the default locale.
     *
     * @return string
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present and no locale set
     */
    public function getLocale()
    {
        if ($this->locale === null) {
            if (!extension_loaded('intl')) {
                throw new Exception\ExtensionNotLoadedException(sprintf(
                    '%s component requires the intl PHP extension',
                    __NAMESPACE__
                    ));
            }
            $this->locale = \Locale::getDefault();
        }
        
        return $this->locale;
    }
    
    /**
     * Get a translated message.
     *
     * @triggers getTranslatedMessage.missing-translation
     * @param    string $message
     * @param    string $textDomain
     * @param    string $locale
     * @return   string|null
     */
    protected function getTranslatedMessage($message, $textDomain, $locale) 
    {
        if ($message === '' || $message === null) {
            return '';
        }
        
        if (!isset($this->messages[$textDomain][$locale])) {
            $this->loadMessages($textDomain, $locale);
        }
        
        if (isset($this->messages[$textDomain][$locale][$message])) {
            return $this->messages[$textDomain][$locale][$message];
        }
        
        // No fallback locale but try with a less specific locale
        if (preg_match('/^([a-z]{2})_(.*)+$/', $locale, $matches)) {
            $rootLocale = $matches[1];
            return $this->translate($message, $textDomain, $rootLocale);
        }
        
        return null;
    }
    
    /**
     * Load messages for a given language and domain.
     *
     * @param    string $textDomain
     * @param    string $locale
     * @return   void
     */
    protected function loadMessages($textDomain, $locale)
    {
        if (!isset($this->messages[$textDomain])) {
            $this->messages[$textDomain] = [];
        }
        
        // TODO: Load from cache
        
        // text domain to language folder name
        if (preg_match('/^iso\-([0-9]+)(|\-[0-9]+)$/', $textDomain, $matches)) {
            $filename = 'iso_' . $matches[1];
            if ((isset($matches[2]) && (!empty($matches[2])))) {
                $filename .= $matches[2];
            }
        } else {
            // Invalid ISO text domain.
            unset($this->messages[$textDomain]);
            return;
        }
        
        $baserDir = dirname(dirname(__DIR__)) . '/language/' . $filename;
        if (!is_dir($baserDir)) {
            // Invalid ISO text domain.
            unset($this->messages[$textDomain]);
            return;
        }
        
        $file = $baserDir . '/' . $locale . '.mo';
        if (!is_file($file)) {
            // Locale is not available
            // We don't unset the text domain as it exists, 
            // it is only the locale which does not exist.
            return;
        }
        
        // Load the file
        $loader = new Loader\Gettext();
        
        if (isset($this->messages[$textDomain][$locale])) {
            $this->messages[$textDomain][$locale]->merge($loader->load($locale, $file));
        } else {
            $this->messages[$textDomain][$locale] = $loader->load($locale, $file);
        }
        
        // TODO: Store in cache
    }
    
    /**
     * Set the default locale.
     *
     * @param  string     $locale
     * @return Translator
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate($message, $textDomain, $locale = null)
    {
        $locale      = ($locale ?: $this->getLocale());
        $translation = $this->getTranslatedMessage($message, $textDomain, $locale);
        
        if ($translation !== null && $translation !== '') {
            return $translation;
        }
        
        return $message;
    }
}