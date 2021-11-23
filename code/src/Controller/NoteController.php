<?php


namespace App\Controller;


use App\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @Route("/notes/add", name="add_new_note", methods={"POST"})
    */

   public function addNote(Request $request) {
     $entityManager = $this->getDoctrine()->getManager();

     $data =json_decode($request->getContent(), true);

      $newNote = new Note();
      $newNote->setTitle($data["title"]);
      $newNote->setTime($data["time"]);
      $newNote->setText($data["text"]);

     $entityManager->persist($newNote);

     $entityManager->flush();

    return new Response('trying to add new note...' . $newNote->getId());

  }

    /**
    * @Route("/notes", name="get_all_notes")
    */

    public function getAllNotes() {
        $notes = $this->getDoctrine()->getRepository( Recipe::class)->findAll();

       $response = [];

       foreach($notes as $note) {
           $response[] = array(
               'id'=> $note->getId(),
               'title'=>$note->geTitle(),
               'time'=>$note->getTime(),
               'text'=>$note->getText(),
            );
        }
        return $this->json($response);
    }

    /**
     * @Route("/notes/{id}", name="find_a_note")
     */

    public function findNote($id){
        $note = $this->getDoctrine()->getRepository( Note::class)->find($id);

        if(!$note){
            throw $this->createNotFoundException(
                'No note was found with this id:' . $id
            );
        } else {
            return $this->json([
                'id' => $note->getId(),
                'title' => $note->getTitle(),
                'time'=> $note->getTime(),
                'text'=> $note->getText(),
            ]);
        }
    }

    /**
     * @Route ("/notes/update/{id}/{title}", name="update_a_note")
     */
    public function editNote($id, $title) {
        $entityManager = $this->getDoctrine()->getManager();
        $note = $this->getDoctrine()->getRepository( Note::class)->find($id);
        if(!$note){
            throw $this->createNotFoundException(
                'No note was found with this id:' . $id
            );
    } else {
            $note->setTitle($title);
            $entityManager->flush();

            return $this->json([
                'message' => 'Updated a note with id' . $id
            ]);
        }
    }

    /**
     * @Route("/notes/delete/{id}", name="delete_a_note")
     */
    public function deleteNote($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $note = $this->getDoctrine()->getRepository(Note::class)->find($id);

        if (!$note) {
            throw $this->createNotFoundException(
                'No note was found with the id: ' . $id
            );
        } else {
            $entityManager->remove($note);
            $entityManager->flush();

            return $this->json([
                'message' => 'Removed the note with id ' . $id
            ]);
     }
 }
}