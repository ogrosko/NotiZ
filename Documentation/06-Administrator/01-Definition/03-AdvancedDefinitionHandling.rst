.. include:: ../../Includes.txt

.. _administrator-definition-advanced:

Advanced definition handling
============================

In some cases the definition must be manipulated using PHP. To do so, a
definition component service is needed, in order to add new sources for the
definition (for instance TypoScript files), or even modifying the definition
object once it has been created.

1. Register a definition component service
------------------------------------------

In a custom extension ``ext_localconf.php`` file, add this piece of code:

.. code-block:: php
    :caption: ``my_extension/ext_localconf.php``

    $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

    $dispatcher->connect(
        \CuyZ\Notiz\Core\Definition\Builder\DefinitionBuilder::class,
        \CuyZ\Notiz\Core\Definition\Builder\DefinitionBuilder::COMPONENTS_SIGNAL,
        \Vendor\MyExtension\Domain\Definition\Builder\Component\DefinitionComponentService::class,
        'registerDefinitionComponents'
    );

2. Create the definition component service
------------------------------------------

Now the actual definition component service can be written:

.. code-block:: php
    :caption: ``my_extension/Classes/Domain/Definition/Builder/Component/DefinitionComponentService.php``

    namespace Vendor\MyExtension\Domain\Definition\Builder\Component;

    use CuyZ\Notiz\Core\Definition\Builder\Component\DefinitionComponents;
    use CuyZ\Notiz\Domain\Definition\Builder\Component\Source\TypoScriptDefinitionSource;
    use TYPO3\CMS\Core\SingletonInterface;

    class DefinitionComponentService implements SingletonInterface
    {
        /**
         * @param DefinitionComponents $definitionComponents
         */
        public function registerDefinitionComponents(DefinitionComponents $definitionComponents)
        {
            if ($this->someCustomCondition()) {
                /** @var TypoScriptDefinitionSource $typoScriptSource */
                $typoScriptSource = $definitionComponents->getSource(TypoScriptDefinitionSource::class);

                $typoScriptSource->addFilePath('EXT:my_extension/Configuration/TypoScript/MyCustomDefinition.typoscript');
            }
        }
    }

.. hint::

    A priority can be given to the file for sorting purposes. The files with the
    highest priority will be handled first; this means that the files with the
    lowest priority have more chance to override definition values.

    By default, a file has a priority of ``0``.

    .. code-block:: php

        // The definition values from `$file2` will override the ones from `$file1`.
        $typoScriptSource->addFilePath($file1, 50);
        $typoScriptSource->addFilePath($file2, 10);

3. Write definition in the file
-------------------------------

.. code-block:: typoscript
    :caption: ``my_extension/Configuration/TypoScript/MyCustomDefinition.typoscript``

    notiz {
        notifications {
            /*
             * Modifying the provided email notification settings…
             */
            entityEmail {
                settings {
                    /*
                     * These recipients will be available by default in every
                     * email notification record.
                     */
                    globalRecipients {
                         10 = webmaster@acme.com
                    }
                }
            }
        }

        eventGroups {
            /*
             * We add a new event group for our custom events.
             */
            my_extension {
                label = Events for My Extension

                events {
                    /*
                     * Contact form is sent
                     * --------------------
                     *
                     * This event is bound to a signal sent by the contact
                     * controller. It contains data about the user who submitted
                     * the form, that will be available in the notifications
                     * markers.
                     */
                    contactFormSent {
                        label = Contact form sent

                        className = MyVendor\MyExtension\Event\ContactFormSentEvent

                        connection {
                            type = signal

                            className = MyVendor\MyContactExtension\Controller\ContactController
                            name = contactFormSent
                        }
                    }
                }
            }
        }
    }
