<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   MIT
 */

namespace saboteur777\deleteentries\controllers;

use Craft;
use craft\base\Element;
use craft\elements\Entry;
use craft\deleteentries\events\DeleteEvent;
use craft\deleteentries\DeleteEntries;
use craft\helpers\DateTimeHelper;
use craft\models\Section;
use craft\web\Controller;
use craft\web\Request;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Delete Entries controller
 */
class DeleteController extends Controller
{
    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected $allowAnonymous = true;

    // Constants
    // =========================================================================

    /**
     * @event DeleteEvent The event that is triggered before an entry is deleted.
     */
    const EVENT_BEFORE_DELETE_ELEMENT = 'beforeDeleteEntry';

    /**
     * @event DeleteEvent The event that is triggered after an entry is deleted.
     */
    const EVENT_AFTER_DELETE_ELEMENT = 'afterDeleteEntry';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->enableCsrfValidation = true;
    }

    /**
     * Deletes an entry.
     *
     * @return Response|null
     * @throws BadRequestHttpException if it's not a post request or the requested section doesn't exist
     * @throws NotFoundHttpException if it's not a front end request
     */
    public function actionIndex()
    {
        $this->requirePostRequest();

        // Only allow front end requests
        $request = Craft::$app->getRequest();
        if (!$request->getIsSiteRequest()) {
            throw new NotFoundHttpException();
        }

        // Get entry ID
        $entryId = $request->getRequiredBodyParam('entryId');
        $entry = Craft::$app->elements->getElementById($entryId);

        // Make sure the section exists
        $sectionId = $request->getRequiredBodyParam('sectionId');
        if (($section = Craft::$app->getSections()->getSectionById($sectionId)) === null) {
            throw new BadRequestHttpException('Section '.$section.' does not exist.');
        }

        // Get entry delete token
        $deleteToken = $request->getRequiredBodyParam('entryToken');
        $entryToken = $entry->reservationToken;

        // Get redirect URL
        $redirectUrl = $request->getBodyParam('redirectUrl') ?? '/';

        if (!$deleteToken === $entryToken) {
            $result = false;
        } else {
            $event = new DeleteEvent(['entry' => $entry]);

            $result = Craft::$app->elements->deleteElementById($entryId);
        }

        return $this->redirect($redirectUrl);
    }

    // Private Methods
    // =========================================================================

    /**
     * Returns a 'success' response.
     *
     * @param Entry $entry
     *
     * @return Response
     */
    private function _returnSuccess(Entry $entry): Response
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_ELEMENT)) {
            $this->trigger(self::EVENT_AFTER_DELETE_ELEMENT, new DeleteEvent([
                'entry' => $entry
            ]));
        }

        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson([
                'success' => true,
                'id' => $entry->id,
                'title' => $entry->title
            ]);
        }

        Craft::$app->getSession()->setNotice(Craft::t('delete-entries', 'Entry deleted.'));
    }
}
