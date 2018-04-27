Description
-----------

This is the description of the tool.

Parameters
----------

-   info

    If this parameter is used, the query will return the list of Usable
    Collections \
     \

-   doc :

    If this parameter is used, the query will return the tool
    documentation.\
    \

-   related : {collectionType:collection?}

    Type of collection and collection on which to base the search. If no
    collection specified, the default one is used. \
     \

-   query : varchar()

    Character string that will be searched in the collection\
    \

-   offset : int()

    Start position of the request\
    \

-   limit : int()

    Number of returned results\
    \

-   order : ASC | DESC

    Sort in ascending or descending order \
    \

-   format : xml | json | html

    Response Format\
    \

-   lang : fr | en

    Tool language\
    \

Output
------

#### ResponseHeader

    array responseHeader {
          Param[] params;
          int status;
          string error;
          float QTime;
        }
        

#### Response

    array response {
          int total;
          int totalQueryReturned;
          int nbFound;
          int offset;
          Doc[] docs;
        }
        

### Output returned if param "info" used

#### Response

    array response {
          int total;
          int nbFound;
          Collection_Type[] docs;
        }
        

#### Collection\_Type

    array Collection_Type {
          int id;
          string ref;
          string name;
          string description;
          int xid_user;
          int defaultColl;
          Collection[] collections;
        }
        

#### Collection

    array Collection {
          int id;
          string ref;
          string name;
          string description;
          int xid_CollectionType;
          dateTime last_update;
        }
        
