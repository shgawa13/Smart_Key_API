<?php

class RentalbookingGatway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }
  
    // add new rental booking
   
    public function addRentalBooking(array $data): int
    {
        $sql = "INSERT INTO RentalBooking (CustomerID, VehicleID, RentalStartDate, RentalEndDate,
                                    PickupLocation, DropoffLocation, InitialRentalDays, 
                                    RentalPricePerDay, InitialTotalDueAmount, InitialCheckNotes) 
                VALUES (:customerId, :vehicleId, :rentalStartDate, :rentalEndDate, :pickupLocation,
                 :dropoffLocation, :initialRentalDays, :rentalPricePerDay, :initialTotalDueAmount, 
                 :initialCheckNotes)";

        $BookingID = 0;
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':customerId', $data['CustomerID']);
            $stmt->bindParam(':vehicleId', $data['VehicleID']);
            $stmt->bindParam(':rentalStartDate', $data['RentalStartDate']);
            $stmt->bindParam(':rentalEndDate', $data['RentalEndDate']);
            $stmt->bindParam(':pickupLocation', $data['PickupLocation']);
            $stmt->bindParam(':dropoffLocation', $data['DropoffLocation']);
            $stmt->bindParam(':initialRentalDays', $data['InitialRentalDays']);
            $stmt->bindParam(':rentalPricePerDay', $data['RentalPricePerDay']);
            $stmt->bindParam(':initialTotalDueAmount', $data['InitialTotalDueAmount']);
            $stmt->bindParam(':initialCheckNotes', $data['InitialCheckNotes']);
            $stmt->execute();

            $BookingID = (int)$this->conn->lastInsertId();
            
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            
        }
        finally
        {
            return $BookingID;
        }
        
    }

    // get all rental bookings
    public function getAllRentalBookings(): array
    {
        $data = [];
        try {
            $query = "SELECT * FROM RentalBooking ORDER BY BookingID ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        finally
        {
            return $data;
        }
        
    }
    
    // get rental booking by ID
    public function getRentalBookingById(int $id): array | bool
    {
        $IsFound = false;
        try {
            $query = "SELECT * FROM RentalBooking WHERE BookingID = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $IsFound = true;
            } else {
                $IsFound = false;
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        finally
        {
            return  $stmt->fetch(PDO::FETCH_ASSOC) ?: $IsFound;
        }
    }

    // update rental booking
    public function updateRentalBooking(array $current, array $new): int
    {
        $sql = "UPDATE RentalBooking SET CustomerID = :customerId, VehicleID = :vehicleId, 
                RentalStartDate = :rentalStartDate, RentalEndDate = :rentalEndDate, 
                PickupLocation = :pickupLocation, DropoffLocation = :dropoffLocation, 
                InitialRentalDays = :initialRentalDays, RentalPricePerDay = :rentalPricePerDay, 
                InitialTotalDueAmount = :initialTotalDueAmount, InitialCheckNotes = :initialCheckNotes 
                WHERE BookingID = :bookingId";

        $affectedRows = 0;
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':customerId', $new['CustomerID'] ?? $current['CustomerID'],
             PDO::PARAM_INT);
            $stmt->bindValue(':vehicleId', $new['VehicleID'] ?? $current['VehicleID'],
             PDO::PARAM_INT);
            $stmt->bindValue(':rentalStartDate', $new['RentalStartDate'] ?? $current['RentalStartDate'],
             PDO::PARAM_STR);
            $stmt->bindValue(':rentalEndDate', $new['RentalEndDate'] ?? $current['RentalEndDate'],
             PDO::PARAM_STR);
            $stmt->bindValue(':pickupLocation', $new['PickupLocation'] ?? $current['PickupLocation'],
             PDO::PARAM_STR);
            $stmt->bindValue(':dropoffLocation', $new['DropoffLocation'] ?? $current['DropoffLocation'],
             PDO::PARAM_STR);
            $stmt->bindValue(':initialRentalDays', $new['InitialRentalDays'] ?? $current['InitialRentalDays'],
             PDO::PARAM_INT);
            $stmt->bindValue(':rentalPricePerDay', $new['RentalPricePerDay'] ?? $current['RentalPricePerDay'],
             PDO::PARAM_STR);
            $stmt->bindValue(':initialTotalDueAmount', $new['InitialTotalDueAmount'] ?? $current['InitialTotalDueAmount'],
             PDO::PARAM_STR);
            $stmt->bindValue(':initialCheckNotes', $new['InitialCheckNotes'] ?? $current['InitialCheckNotes'],
             PDO::PARAM_STR);
            $stmt->bindValue(':bookingId', $current['BookingID'] ?? $new['BookingID'],
             PDO::PARAM_INT);

            if ($stmt->execute()) {
                // here we return the number of affected rows
                $affectedRows = (int)$stmt->rowCount();
            }
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            
        }
        finally
        {
            return $affectedRows;
        }
        
    }

    // delete rental booking
    public function deleteRentalBooking(int $id): int
    {
        $sql = "DELETE FROM RentalBooking WHERE BookingID = :bookingId";

        $affectedRows = 0;
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':bookingId', $id);

            if ($stmt->execute()) {
                // here we return the number of affected rows
                $affectedRows = (int)$stmt->rowCount();
            }
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            
        }
        finally
        {
            return $affectedRows;
        }
        
    }

}